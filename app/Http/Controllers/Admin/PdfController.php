<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionPaper;
use Illuminate\Http\Request;
use PDF;

class PdfController extends Controller
{
    /**
     * Generate a PDF for a question paper.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generateQuestionPaperPdf(Request $request, $id)
    {
        $questionPaper = QuestionPaper::with([
            'subject',
            'sections.questions' => function ($query) {
                $query->orderBy('order');
            },
            'sections.questions.topic',
            'sections.questions.blooms_taxonomy'
        ])->findOrFail($id);
        
        $pdf = PDF::loadView('admin.question_papers.pdf.question_paper', [
            'questionPaper' => $questionPaper
        ]);
        
        $filename = str_replace(' ', '_', $questionPaper->title) . '.pdf';
        
        return $pdf->download($filename);
    }
}
