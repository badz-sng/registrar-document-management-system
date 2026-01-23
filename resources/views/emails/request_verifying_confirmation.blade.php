<h1>Request Update</h1>
<p>Hello {{ $request->student->name }},</p>
<p>Your document request has been prepared and is currently under verification for release.</p>

<h3>Request Details:</h3>
<ul>
    <li><strong>Student Name:</strong> {{ $request->student->name }}</li>
    <li><strong>Status:</strong> Verifying</li>
    <li><strong>Estimated Release Date:</strong> {{ $request->estimated_release_date->format('F d, Y') ?? 'To be determined' }}</li>
</ul>

<p>We will notify you once your documents have been verified and are ready for pickup.</p>
