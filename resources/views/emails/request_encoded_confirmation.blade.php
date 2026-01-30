<h1>Request Confirmation</h1>
<p>Hello {{ $request->student->name }},</p>
<p>Your document request has been successfully encoded and is now being processed.</p>

<h3>Request Details:</h3>
<ul>
    <li><strong>Student Name:</strong> {{ $request->student->name }}</li>
    @if($request->representative_name)
        <li><strong>Representative:</strong> {{ $request->representative_name }}</li>
    @endif
    <li><strong>Requested Document(s):</strong></li>
    <ul>
        @foreach($request->documentTypes as $doc)
            <li>{{$doc->name}}</li>
        @endforeach
    </ul>
</ul>
<p>We will notify you once your documents are ready for pickup.</p> 