<h1>Request Confirmation</h1>
<p>Hello {{ $request->student->name }},</p>
<p>Your document request has been successfully encoded and is now being processed.</p>

<h3>Request Details:</h3>
<ul>
    <li><strong>Student Name:</strong> {{ $request->student->name }}</li>
    <li><strong>Requested Document(s):</strong></li>
    <ul>
        @foreach($request->documentTypes as $doc){
            <li>{{$doc->name}}</li>
        }
        @endforeach
    </ul>
    <li><strong>Estimated Release Date:</strong> {{ $request->estimated_release_date->format('F d, Y') }}</li>
</ul>

<p>We will notify you once your documents are ready for pickup.</p>