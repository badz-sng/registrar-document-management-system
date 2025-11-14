# Current Implementation Overview & Function Reference
**Date:** November 14, 2025  
**Purpose:** Catch you up on the codebase after 3 weeks away. Explains what's been implemented, how routes/controllers work, and where to pick up work.

---

## Quick Start: Where Things Are

| What | Where |
|------|-------|
| **Routes & entry points** | `routes/web.php` |
| **Role-based controllers** | `app/Http/Controllers/{Admin,Encoder,Processor,Verifier,Retriever}/DashboardController.php` |
| **Request handling** | `app/Http/Controllers/RequestController.php` |
| **Data models** | `app/Models/{User,RequestModel,Student,DocumentType,etc}.php` |
| **Views/UI** | `resources/views/{admin,encoder,processor,verifier,retriever}/dashboard.blade.php` |
| **Processing logic** | `app/Helpers/ProcessingDays.php`, `app/Http/Controllers/RequestController.php` |
| **Database schema** | `database/migrations/` |

---

## High-Level Architecture

### User Roles
The system has **5 roles** (defined in `app/Models/User.php`):
```php
ROLE_ADMIN      = 'admin'      // Full system access, user management, approvals
ROLE_ENCODER    = 'encoder'    // Creates/encodes student requests
ROLE_PROCESSOR  = 'processor'  // Prepares documents (marked not yet implemented)
ROLE_VERIFIER   = 'verifier'   // Verifies prepared documents (marked not yet implemented)
ROLE_RETRIEVER  = 'retriever'  // Marks requests as retrieved for final release
```

### Request Workflow (High Level)
```
Encoder creates request → Processor prepares documents → Verifier checks & signs
→ Retriever marks retrieved → Admin approves for release
```

---

## Routes & Entry Points

### Home Route
**File:** `routes/web.php` line 22–39

```php
Route::get('/', function () {
    if (!auth()->check()) return redirect('/login');
    
    return match (auth()->user()->role) {
        'encoder' => redirect()->route('encoder.dashboard'),
        'processor' => redirect()->route('processor.dashboard'),
        'verifier' => redirect()->route('verifier.dashboard'),
        'admin' => redirect()->route('admin.dashboard'),
        'retriever' => redirect()->route('retriever.dashboard'),
        default => redirect('/login'),
    };
});
```

**What it does:** When user visits `/`, redirects them to their role-specific dashboard.

---

### ADMIN Routes & Functions

**Prefix:** `/admin`  
**Middleware:** `auth`, `role:admin`  
**File:** `routes/web.php` lines 47–62

| Route | Controller Method | Purpose |
|-------|------------------|---------|
| `GET /admin/dashboard` | `Admin\DashboardController@index()` | Show admin dashboard (summary of all requests, users) |
| `GET /admin/users` | `Admin\DashboardController@users()` | List all users for management |
| `GET /admin/for-release` | `Admin\DashboardController@forRelease()` | List requests ready for signature/release |
| `PATCH /admin/for-release/{requestId}/toggle-signed/{documentId}` | `Admin\DashboardController@toggleSigned()` | Toggle document signed status |

**Current Implementation Status:**
- Dashboard view exists: `resources/views/admin/dashboard.blade.php`
- User list view exists: `resources/views/admin/users.blade.php`
- Release list view exists: `resources/views/admin/for-release.blade.php`
- Controllers mostly empty stubs (need implementation for data retrieval and logic)

---

### ENCODER Routes & Functions

**Prefix:** `/encoder`  
**Middleware:** `auth`, `role:encoder`  
**File:** `routes/web.php` lines 67–72

| Route | Controller Method | Purpose |
|-------|------------------|---------|
| `GET /encoder/dashboard` | `Encoder\DashboardController@index()` | Show encoder dashboard |
| `GET /encoder/requests` | `RequestController@index()` | List encoder's requests |
| `GET /encoder/requests/create` | `RequestController@create()` | Show request creation form |
| `POST /encoder/requests/store` | `RequestController@store()` | **Save new request** ← Main encode logic |

#### **ENCODER::CREATE NEW REQUEST** (The Core Encoding Function)

**File:** `app/Http/Controllers/RequestController.php` lines 34–160  
**Route:** `POST /encoder/requests/store`  
**View:** `resources/views/encoder/dashboard.blade.php`

**Form Fields:**
```
Last Name, First Name, Middle Name (split name fields)
Course, Year Level, Address, Contact Number, Email
Last School Year Attended (optional)
Student Number (defaults to empty string)
Document Types (checkboxes, multiple select)
  → Supports "Other" with custom input field
Is Representative? (checkbox)
  → If yes, show Representative Name field
```

**What the `store()` function does:**

1. **Validates input**
   ```php
   $validated = $request->validate([
       'last_name' => 'required',
       'first_name' => 'required',
       'middle_name' => 'nullable',
       'course' => 'required',
       'year_level' => 'required',
       'address' => 'required',
       'contact_number' => 'required',
       'email' => 'required|email',
       'document_type_id' => 'required|array',  // Multi-select!
       'document_type_other' => 'nullable',      // Custom type
       'representative_name' => 'nullable',
       'last_school_year' => 'nullable',
   ]);
   ```

2. **Creates/finds Student**
   ```php
   // Build full name: "LastName, FirstName MiddleName"
   $studentName = trim($validated['last_name'] . ', ' . $validated['first_name'] . ' ' . ($validated['middle_name'] ?? ''));
   
   // Find or create Student record
   $student = Student::firstOrCreate([
       'name' => $studentName,
       'course' => $validated['course'],
       'year_level' => $validated['year_level'],
       'address' => $validated['address'],
       'contact_number' => $validated['contact_number'],
       'email' => $validated['email'],
       'student_no' => $validated['student_no'] ?? '',
       'last_school_year' => $validated['last_school_year'] ?? null,
   ]);
   ```

3. **Collects document type IDs**
   - Loops through selected document types
   - Skips any 'other' placeholder
   - If "Other" checkbox checked, creates new DocumentType on-the-fly
   - Stores all IDs in `$documentTypeIds` array

4. **Calculates release date**
   ```php
   // Get processing days for each selected document type
   $processingDaysList = [];
   foreach ($documentTypeIds as $docTypeId) {
       $docType = DocumentType::findOrFail($docTypeId);
       $processingDaysList[] = $this->getProcessingDays($docType->name);
   }
   
   // Use max (e.g., if one doc needs 10 days, another 5, use 10)
   $maxProcessingDays = max($processingDaysList);
   
   // Calculate release date (skips weekends & PH holidays)
   $releaseDate = $this->calculateReleaseDate(now(), $maxProcessingDays);
   ```

5. **Applies cutoff logic**
   - If 10+ requests already scheduled for same doc type on same release date
   - Push release date forward 1 day (respecting weekends/holidays)

6. **Creates RequestModel**
   ```php
   RequestModel::create([
       'student_id' => $student->id,
       'document_type_id' => $documentTypeIds[0],      // Legacy single field
       'document_type_ids' => $documentTypeIds,        // NEW: JSON array
       'representative_name' => $repName,
       'encoded_by' => auth()->id(),                   // Current encoder
       'status' => 'Pending',
       'encoded_at' => now(),
       'estimated_release_date' => $releaseDate,
   ]);
   
   // Attach to pivot table for many-to-many relationship
   $requestModel->documentTypes()->attach($documentTypeIds);
   ```

7. **Returns success message**
   ```php
   return redirect()->route('requests.index')->with('success', 'Requests recorded successfully!');
   ```

**Processing Days Logic:**
```php
private function getProcessingDays($documentName)
{
    return match (strtolower($documentName)) {
        'f-137' => 10,
        'f-138' => 5,
        'tor' => 10,
        'transfer credential' => 4,
        'good moral certificate' => 7,
        'diploma' => 7,
        'certificate of grades' => 10,
        'certificate of enrollment' => 4,
        'certificate of graduation' => 4,
        'honorable dismissal' => 4,
        default => 5,
    };
}
```

**Release Date Calculation:**
- Starts from NOW
- Adds X business days (where X = max processing days for all selected doc types)
- Skips weekends (Sat/Sun)
- Skips Philippine holidays: Jan 1, Apr 17–18, Jun 12, Nov 1, Dec 25, Dec 30

---

### PROCESSOR Routes & Functions

**Prefix:** `/processor`  
**Middleware:** `auth`, `role:processor`  
**File:** `routes/web.php` lines 77–87

| Route | Controller Method | Purpose |
|-------|------------------|---------|
| `GET /processor/dashboard` | `Processor\DashboardController@index()` | Show processor dashboard |
| `POST /processor/requests/{id}/mark-prepared` | `Processor\DashboardController@markAsPrepared()` | **NOT YET IMPLEMENTED** |
| `POST /processor/requests/{request}/documents/{document}/toggle` | `RequestController@togglePrepared()` | Toggle document prepared flag |

**Current Status:** Views exist (`resources/views/processor/dashboard.blade.php`) but controller methods mostly empty stubs.

---

### VERIFIER Routes & Functions

**Prefix:** `/verifier`  
**Middleware:** `auth`, `role:verifier`  
**File:** `routes/web.php` lines 92–105

| Route | Controller Method | Purpose |
|-------|------------------|---------|
| `GET /verifier/dashboard` | `Verifier\DashboardController@index()` | Show verifier dashboard |
| `POST /verifier/toggle/{requestId}/{documentId}` | `Verifier\DashboardController@toggleVerification()` | **NOT YET IMPLEMENTED** |

**Current Status:** View exists (`resources/views/verifier/dashboard.blade.php`) but controller mostly empty.

---

### RETRIEVER Routes & Functions

**Prefix:** `/retriever`  
**Middleware:** `auth`, `role:retriever`  
**File:** `routes/web.php` lines 110–120

| Route | Controller Method | Purpose |
|-------|------------------|---------|
| `GET /retriever/dashboard` | `Retriever\DashboardController@index()` | Show retriever dashboard |
| `POST /retriever/requests/{id}/retrieve` | `Retriever\DashboardController@updateStatus()` | **Mark request as retrieved** |

#### **RETRIEVER::MARK AS RETRIEVED** (Implemented)

**File:** `app/Http/Controllers/Retriever/DashboardController.php`  
**View:** `resources/views/retriever/dashboard.blade.php`

**What it does:**
1. Finds RequestModel by ID
2. Checks authorization: only allow if `retriever_id` is NULL or equals current user
3. Sets `status = 'retrieved'` and `retriever_id = auth()->id()`
4. Returns redirect with success message
5. View shows "Mark as Retrieved" button only for requests with `status = 'pending'`

---

## Data Models

### RequestModel (`app/Models/RequestModel.php`)

**Table:** `requests`

**Key Fields:**
```php
$fillable = [
    'student_id',              // FK to Student
    'representative_id',       // FK to Representative (optional)
    'document_type_id',        // Legacy single doc type
    'document_type_ids',       // NEW: JSON array of doc type IDs
    'representative_name',     // Text field for representative name
    'authorization_id',        // FK to Authorization
    'status',                  // Enum: Pending, in_process, ready_for_verification, verified, retrieved, released
    'encoded_by',              // FK to User (encoder who created this)
    'retriever_id',            // FK to User (retriever assigned)
    'processor_id',            // FK to User (processor assigned)
    'verifier_id',             // FK to User (verifier assigned)
    'verified_at',             // Timestamp
    'encoded_at',              // Timestamp
    'estimated_release_date',  // DateTime
];

protected $casts = [
    'document_type_ids' => 'array',           // JSON → PHP array
    'estimated_release_date' => 'datetime',
    'encoded_at' => 'datetime',
    'verified_at' => 'datetime',
];
```

**Relationships:**
```php
$request->student()              // BelongsTo Student
$request->documentTypes()        // BelongsToMany DocumentType (pivot table)
$request->encoder()              // BelongsTo User (via encoded_by)
$request->retriever()            // BelongsTo User (via retriever_id)
$request->processor()            // BelongsTo User (via processor_id)
$request->verifier()             // BelongsTo User (via verifier_id)
$request->processingLogs()       // HasMany ProcessingLog
$request->releaseRecord()        // HasOne ReleaseRecord
```

### Student (`app/Models/Student.php`)

**Table:** `students`

**Fields:**
```
id, student_no, name, email, course, year_level, 
address, contact_number, last_school_year, created_at, updated_at
```

### DocumentType (`app/Models/DocumentType.php`)

**Table:** `document_types`

**Fields:**
```
id, name, description, processing_category 
(processing_category: 'certificate' | 'ctc' | 'transcript')
```

### User (`app/Models/User.php`)

**Role Constants:**
```php
const ROLE_ADMIN = 'admin';
const ROLE_ENCODER = 'encoder';
const ROLE_PROCESSOR = 'processor';
const ROLE_VERIFIER = 'verifier';
const ROLE_RETRIEVER = 'retriever';

const ROLES = [
    self::ROLE_ADMIN,
    self::ROLE_ENCODER,
    self::ROLE_PROCESSOR,
    self::ROLE_VERIFIER,
    self::ROLE_RETRIEVER,
];
```

---

## Database Schema Overview

**Key Tables:**
- `users` — system users with roles
- `students` — student records
- `document_types` — available document types
- `requests` — the main request records
- `request_document` — many-to-many pivot (requests ↔ document_types)
- `processing_logs` — audit trail of request processing
- `activity_logs` — system-wide activity log
- `notifications` — user notifications
- `release_records` — final release records
- `representatives` — representative information
- `authorizations` — authorization records

---

## Views & UI Files

### Encoder Dashboard (`resources/views/encoder/dashboard.blade.php`)
- **Form:** Request creation form with all input fields
- **Table:** Shows list of requests encoded by current user
- **Columns:** Student name, Document types, Status, Release date
- **Features:** Multi-select checkboxes for doc types, "Other" field, representative toggle

### Admin Dashboard (`resources/views/admin/dashboard.blade.php`)
- Layout file (actual content/queries need implementation in controller)

### Admin Users (`resources/views/admin/users.blade.php`)
- Layout file for user management

### Admin For-Release (`resources/views/admin/for-release.blade.php`)
- Layout file for release document management

### Retriever Dashboard (`resources/views/retriever/dashboard.blade.php`)
- Shows pending requests
- "Mark as Retrieved" button (only for pending requests)

### Processor Dashboard (`resources/views/processor/dashboard.blade.php`)
- Placeholder view (needs implementation)

### Verifier Dashboard (`resources/views/verifier/dashboard.blade.php`)
- Placeholder view (needs implementation)

---

## Helper Functions & Utilities

### ProcessingDays Helper (`app/Helpers/ProcessingDays.php`)
- **Purpose:** Centralized business logic for processing time calculations
- **Usage:** Referenced in RequestController for calculating release dates

---

## Current Issues & Missing Implementations

### ✅ IMPLEMENTED & WORKING
- ✅ Encoder role: create requests with multiple document types
- ✅ Encoder form: split name fields, multiple doc types, representative support
- ✅ Release date calculation: business days + holiday exclusion + cutoff logic
- ✅ Retriever role: mark request as retrieved
- ✅ Role-based routing: middleware enforces access control
- ✅ Multi-select document types: JSON storage + pivot table support
- ✅ Processor dashboard & functions (mark as prepared, etc.)
- ✅ Verifier dashboard & functions (verify, sign, etc.)
- ✅ Request status transitions & workflow enforcement

### ❌ NOT YET IMPLEMENTED
- ❌ Admin full functionality (user CRUD, release approval, etc.)
- ❌ Student data management UI (view/edit student profiles)
- ❌ Notifications & email alerts
- ❌ Search & filtering (across admin, processor, verifier)
- ❌ Bulk operations (process multiple requests at once)
- ❌ PDF/document generation
- ❌ Audit trail UI (activity log viewer)

---

## Getting Started Again: Key Tasks

### If you want to continue encoding:
1. Login as encoder user
2. Go to `/encoder/dashboard`
3. Fill out form and submit — should create request with multiple doc types

### If you want to implement processor workflow:
1. Look at `app/Http/Controllers/Processor/DashboardController.php` — currently empty
2. Implement `index()` to fetch requests with `status = 'in_process'` or `status = 'Pending'`
3. Implement `markAsPrepared()` to update request status
4. Update view: `resources/views/processor/dashboard.blade.php`

### If you want to implement verifier workflow:
1. Look at `app/Http/Controllers/Verifier/DashboardController.php` — currently empty
2. Implement `index()` to fetch requests ready for verification
3. Implement `toggleVerification()` to mark documents as verified
4. Update view: `resources/views/verifier/dashboard.blade.php`

### If you want to implement admin functions:
1. Implement `Admin\DashboardController` methods to query data
2. Update views with actual data binding and UI
3. Implement user CRUD operations
4. Implement release approval workflow

---

## Key Files to Remember

| File | Purpose | Status |
|------|---------|--------|
| `routes/web.php` | All route definitions | ✅ Complete |
| `app/Http/Controllers/RequestController.php` | Encoder request creation | ✅ Implemented |
| `app/Http/Controllers/Encoder/DashboardController.php` | Encoder dashboard | ✅ Basic |
| `app/Http/Controllers/Processor/DashboardController.php` | Processor workflow | ❌ Empty stub |
| `app/Http/Controllers/Verifier/DashboardController.php` | Verifier workflow | ❌ Empty stub |
| `app/Http/Controllers/Admin/DashboardController.php` | Admin functions | ❌ Empty stub |
| `app/Http/Controllers/Retriever/DashboardController.php` | Retriever workflow | ✅ Implemented |
| `app/Models/RequestModel.php` | Core request model | ✅ Complete |
| `app/Models/User.php` | User model + role constants | ✅ Complete |
| `resources/views/encoder/dashboard.blade.php` | Encoder UI | ✅ Complete |
| `resources/views/processor/dashboard.blade.php` | Processor UI | ❌ Placeholder |
| `resources/views/verifier/dashboard.blade.php` | Verifier UI | ❌ Placeholder |
| `resources/views/admin/dashboard.blade.php` | Admin UI | ❌ Placeholder |

---

## Next Recommended Steps

1. **Test current encoder functionality** — verify requests create properly with multiple doc types
2. **Implement Processor workflow** — mark documents as prepared, update status to ready_for_verification
3. **Implement Verifier workflow** — verify documents, sign, update status to verified
4. **Implement Admin functions** — handle release approval and user management
5. **Add search/filter** — across all dashboards for better usability
6. **Add notifications** — email/in-system alerts for workflow transitions

---

**Questions?** Check the route definitions in `routes/web.php` or controller files for more context on what's connected where.

