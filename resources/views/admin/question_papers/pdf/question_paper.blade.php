<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $questionPaper->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }
        
        .institution-name {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .exam-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .exam-details {
            font-size: 12pt;
            margin-bottom: 5px;
        }
        
        .instructions {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #000;
            background-color: #f9f9f9;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }
        
        .section-instructions {
            font-style: italic;
            margin-bottom: 10px;
        }
        
        .question {
            margin-bottom: 15px;
        }
        
        .question-number {
            font-weight: bold;
            display: inline-block;
            width: 30px;
            vertical-align: top;
        }
        
        .question-content {
            display: inline-block;
            width: calc(100% - 35px);
            vertical-align: top;
        }
        
        .question-text {
            margin-bottom: 5px;
        }
        
        .options {
            margin-left: 20px;
        }
        
        .option {
            margin-bottom: 5px;
        }
        
        .marks {
            float: right;
            font-weight: bold;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .answer-key {
            margin-top: 20px;
        }
        
        .answer-key-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .answer-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .answer-table th, .answer-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        
        .marking-scheme {
            margin-top: 20px;
        }
        
        .marking-scheme-title {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .footer {
            text-align: center;
            font-size: 10pt;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ccc;
        }
        
        @page {
            margin: 2cm;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="institution-name">{{ config('app.name', 'Exam Seat Management System') }}</div>
        <div class="exam-title">{{ $questionPaper->title }}</div>
        <div class="exam-details">
            <strong>Subject:</strong> {{ $questionPaper->subject->subject_name }} | 
            <strong>Total Marks:</strong> {{ $questionPaper->total_marks }} | 
            <strong>Duration:</strong> {{ $questionPaper->duration }} minutes
            @if($questionPaper->exam_date)
                | <strong>Date:</strong> {{ $questionPaper->exam_date->format('d/m/Y') }}
            @endif
        </div>
    </div>
    
    @if($questionPaper->additional_instructions)
        <div class="instructions">
            <strong>General Instructions:</strong>
            <div>{!! $questionPaper->additional_instructions !!}</div>
        </div>
    @endif
    
    @foreach($questionPaper->sections as $sectionIndex => $section)
        <div class="section">
            <div class="section-title">{{ $section->title }} ({{ $section->total_marks }} marks)</div>
            
            @if($section->instructions)
                <div class="section-instructions">{{ $section->instructions }}</div>
            @endif
            
            @foreach($section->questions as $questionIndex => $question)
                <div class="question">
                    <div class="question-number">{{ $questionIndex + 1 }}.</div>
                    <div class="question-content">
                        <div class="question-text">{!! $question->question_text !!}</div>
                        
                        @if($question->question_type == 'mcq' && $question->options)
                            <div class="options">
                                @foreach(json_decode($question->options) as $optionIndex => $option)
                                    <div class="option">
                                        {{ chr(97 + $optionIndex) }}) {{ $option }}
                                    </div>
                                @endforeach
                            </div>
                        @elseif($question->question_type == 'true_false')
                            <div class="options">
                                <div class="option">a) True</div>
                                <div class="option">b) False</div>
                            </div>
                        @elseif($question->question_type == 'fill_in_the_blank')
                            <!-- Space for answer -->
                            <div style="border-bottom: 1px solid #000; margin: 10px 0;"></div>
                        @elseif($question->question_type == 'short_answer')
                            <!-- Space for answer -->
                            <div style="height: 50px;"></div>
                        @elseif($question->question_type == 'long_answer')
                            <!-- Space for answer -->
                            <div style="height: 150px;"></div>
                        @endif
                        
                        <div class="marks">[{{ $question->marks }} marks]</div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
    
    @if($questionPaper->include_answer_key)
        <div class="page-break"></div>
        <div class="answer-key">
            <div class="answer-key-title">Answer Key</div>
            
            @foreach($questionPaper->sections as $sectionIndex => $section)
                <div class="section">
                    <div class="section-title">{{ $section->title }}</div>
                    
                    <table class="answer-table">
                        <thead>
                            <tr>
                                <th>Question No.</th>
                                <th>Answer</th>
                                <th>Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($section->questions as $questionIndex => $question)
                                <tr>
                                    <td>{{ $questionIndex + 1 }}</td>
                                    <td>
                                        @if($question->question_type == 'mcq' && $question->correct_option !== null)
                                            {{ chr(97 + $question->correct_option) }}
                                        @elseif($question->question_type == 'true_false')
                                            {{ $question->correct_option ? 'True' : 'False' }}
                                        @elseif($question->answer)
                                            {{ $question->answer }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $question->marks }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endif
    
    @if($questionPaper->include_marking_scheme)
        <div class="page-break"></div>
        <div class="marking-scheme">
            <div class="marking-scheme-title">Marking Scheme</div>
            
            @foreach($questionPaper->sections as $sectionIndex => $section)
                <div class="section">
                    <div class="section-title">{{ $section->title }}</div>
                    
                    @foreach($section->questions as $questionIndex => $question)
                        <div class="question">
                            <div class="question-number">{{ $questionIndex + 1 }}.</div>
                            <div class="question-content">
                                <div class="question-text">{!! $question->question_text !!}</div>
                                
                                @if($question->marking_scheme)
                                    <div style="margin-top: 10px;">
                                        <strong>Marking Scheme:</strong>
                                        <div>{!! $question->marking_scheme !!}</div>
                                    </div>
                                @endif
                                
                                <div class="marks">[{{ $question->marks }} marks]</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if(!$loop->last)
                    <div class="page-break"></div>
                @endif
            @endforeach
        </div>
    @endif
    
    <div class="footer">
        {{ config('app.name', 'Exam Seat Management System') }} | Generated on {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
