<h1>Request Ready for Release</h1>
<p>Hello {{ $request->student->name }},</p>
<p>Your document request has been verified and is now ready for release. You can pick up your documents at your earliest convenience.</p>

<h3>Request Details:</h3>
<ul>
    <li><strong>Student Name:</strong> {{ $request->student->name }}</li>
    <li><strong>Status:</strong> Ready for Release</li>
    <li><strong>Release Date:</strong> {{ $request->estimated_release_date->format('F d, Y') ?? 'Available now' }}</li>
</ul>

<p>Please visit our office during business hours to collect your documents. If you have any questions, feel free to contact us.</p>
