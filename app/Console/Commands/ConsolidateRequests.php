<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RequestModel;
use Illuminate\Support\Facades\DB;

class ConsolidateRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'requests:consolidate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consolidate multiple request rows into single rows with document_type_ids JSON array';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting consolidation...');

        // Grouping criteria: identical student_id, representative_id, authorization_id, encoded_by, status, estimated_release_date
        $groups = RequestModel::selectRaw('student_id, representative_id, authorization_id, encoded_by, status, estimated_release_date, COUNT(*) as cnt')
            ->groupBy('student_id', 'representative_id', 'authorization_id', 'encoded_by', 'status', 'estimated_release_date')
            ->having('cnt', '>', 1)
            ->get();

        $this->info('Found ' . $groups->count() . ' groups to consolidate');

        foreach ($groups as $g) {
            $rows = RequestModel::where('student_id', $g->student_id)
                ->where('representative_id', $g->representative_id)
                ->where('authorization_id', $g->authorization_id)
                ->where('encoded_by', $g->encoded_by)
                ->where('status', $g->status)
                ->where('estimated_release_date', $g->estimated_release_date)
                ->get();

            $docIds = [];
            foreach ($rows as $r) {
                if ($r->document_type_ids && is_array($r->document_type_ids)) {
                    $docIds = array_merge($docIds, $r->document_type_ids);
                } elseif ($r->document_type_id) {
                    $docIds[] = $r->document_type_id;
                }
            }

            $docIds = array_values(array_unique($docIds));

            // Create consolidated row (keep the first row's other attributes)
            $first = $rows->first();
            $data = $first->replicate()->toArray();
            $data['document_type_id'] = count($docIds) ? $docIds[0] : $first->document_type_id;
            $data['document_type_ids'] = $docIds;

            DB::transaction(function () use ($rows, $data) {
                RequestModel::create($data);
                // delete old rows
                foreach ($rows as $r) {
                    $r->delete();
                }
            });

            $this->info('Consolidated group for student_id=' . $g->student_id . ' into ' . count($docIds) . ' docs');
        }

        $this->info('Consolidation complete.');
        return 0;
    }
}
