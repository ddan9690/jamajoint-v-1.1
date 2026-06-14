<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        /* A4 paper, minimal margins to maximize data area */
        @page { margin: 25px; }
        body { font-family: sans-serif; font-size: 10px; }
        
        .page-break { page-break-after: always; }
        .header { text-align: center; margin-bottom: 10px; }
        .school-name { font-size: 16px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .stream-name { font-size: 13px; font-weight: bold; margin: 3px 0; }
        .exam-details { font-size: 11px; margin: 0; color: #333; }
        
        /* Table styling - maximizing density */
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #444; padding: 3px 4px; text-align: left; vertical-align: middle; }
        th { background-color: #eee; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        
        /* Column sizing */
        .col-num { width: 25px; }
        .col-adm { width: 65px; }
        .col-stream { width: 55px; }
        
        /* Page numbering */
        .page-number { position: fixed; bottom: -10px; right: 0px; font-size: 9px; }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    <div class="page-number">Page </div>

    @forelse($streams as $stream)
        <div class="header">
            <h1 class="school-name">{{ $school->name }}</h1>
            <h2 class="stream-name">Stream: {{ $stream->name }}</h2>
            <p class="exam-details">
                {{ $exam->name }} | {{ $exam->term->name ?? 'N/A' }} | {{ $exam->academicYear->year ?? 'N/A' }}
            </p>
        </div>

        @if($stream->students->isEmpty())
            <p style="text-align: center; color: #666; margin-top: 20px;">No students found in this stream.</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th class="col-num">#</th>
                        <th class="col-adm">Adm</th>
                        <th>Name</th>
                        <th class="col-stream">Stream</th>
                        @foreach($exam->subject->papers as $paper)
                            <th>{{ $paper->name }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($stream->students as $index => $student)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $student->admission_number }}</td>
                        <td>{{ $student->name }}</td>
                        <td class="text-center">{{ $stream->name }}</td>
                        @foreach($exam->subject->papers as $paper)
                            <td>&nbsp;</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @empty
        <div class="header">
            <h1 class="school-name">{{ $school->name }}</h1>
        </div>
        <p style="text-align: center; font-size: 14px; margin-top: 50px;">
            No stream or student data available for this school in this exam.
        </p>
    @endforelse
</body>
</html>