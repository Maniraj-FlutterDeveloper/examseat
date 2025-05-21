<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportService
{
    /**
     * Generate a report.
     *
     * @param  \App\Models\Report  $report
     * @param  array  $parameters
     * @return \App\Models\ReportResult
     */
    public function generateReport(Report $report, array $parameters = [])
    {
        try {
            // Merge the report parameters with the provided parameters
            $mergedParameters = array_merge($report->parameters ?? [], $parameters);
            
            // Get the report data based on the report type
            $data = $this->getReportData($report->type, $mergedParameters);
            
            // Generate the report file if needed
            $filePath = null;
            $fileType = null;
            
            if (isset($parameters['format']) && in_array($parameters['format'], ['excel', 'pdf'])) {
                if ($parameters['format'] === 'excel') {
                    list($filePath, $fileType) = $this->generateExcelReport($report, $data);
                } else {
                    list($filePath, $fileType) = $this->generatePdfReport($report, $data);
                }
            }
            
            // Create a new report result
            $result = ReportResult::create([
                'report_id' => $report->id,
                'data' => $data,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'generated_at' => now(),
                'status' => 'success',
            ]);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage(), [
                'report_id' => $report->id,
                'parameters' => $parameters,
                'exception' => $e,
            ]);
            
            // Create a failed report result
            $result = ReportResult::create([
                'report_id' => $report->id,
                'generated_at' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            
            return $result;
        }
    }

    /**
     * Get the report data based on the report type.
     *
     * @param  string  $type
     * @param  array  $parameters
     * @return array
     */
    protected function getReportData($type, array $parameters = [])
    {
        switch ($type) {
            case 'exam_statistics':
                return $this->getExamStatisticsData($parameters);
            case 'seating_plan':
                return $this->getSeatingPlanData($parameters);
            case 'question_paper':
                return $this->getQuestionPaperData($parameters);
            case 'student_performance':
                return $this->getStudentPerformanceData($parameters);
            case 'custom':
                return $this->getCustomReportData($parameters);
            default:
                throw new \InvalidArgumentException("Unsupported report type: {$type}");
        }
    }

    /**
     * Get exam statistics data.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getExamStatisticsData(array $parameters = [])
    {
        $data = [
            'title' => 'Exam Statistics Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'parameters' => $parameters,
            'statistics' => [],
        ];
        
        // Get the total number of exams
        $data['statistics']['total_exams'] = DB::table('seating_plans')->count();
        
        // Get the total number of students
        $data['statistics']['total_students'] = DB::table('students')->count();
        
        // Get the total number of rooms
        $data['statistics']['total_rooms'] = DB::table('rooms')->count();
        
        // Get the total number of blocks
        $data['statistics']['total_blocks'] = DB::table('blocks')->count();
        
        // Get the total number of courses
        $data['statistics']['total_courses'] = DB::table('courses')->count();
        
        // Get the average number of students per exam
        $data['statistics']['avg_students_per_exam'] = DB::table('seating_plans')
            ->join('students', 'seating_plans.student_id', '=', 'students.id')
            ->select(DB::raw('COUNT(DISTINCT students.id) as student_count'))
            ->groupBy('seating_plans.id')
            ->avg('student_count') ?? 0;
        
        // Get the average number of rooms per exam
        $data['statistics']['avg_rooms_per_exam'] = DB::table('seating_plans')
            ->join('rooms', 'seating_plans.room_id', '=', 'rooms.id')
            ->select(DB::raw('COUNT(DISTINCT rooms.id) as room_count'))
            ->groupBy('seating_plans.id')
            ->avg('room_count') ?? 0;
        
        // Get the distribution of students by course
        $data['statistics']['students_by_course'] = DB::table('students')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->select('courses.name', DB::raw('COUNT(students.id) as count'))
            ->groupBy('courses.id', 'courses.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
        
        // Get the distribution of rooms by block
        $data['statistics']['rooms_by_block'] = DB::table('rooms')
            ->join('blocks', 'rooms.block_id', '=', 'blocks.id')
            ->select('blocks.name', DB::raw('COUNT(rooms.id) as count'))
            ->groupBy('blocks.id', 'blocks.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
        
        // Get the recent exams
        $data['statistics']['recent_exams'] = DB::table('seating_plans')
            ->select('id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
        
        return $data;
    }

    /**
     * Get seating plan data.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getSeatingPlanData(array $parameters = [])
    {
        $data = [
            'title' => 'Seating Plan Analytics Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'parameters' => $parameters,
            'statistics' => [],
        ];
        
        // Get the total number of seating plans
        $data['statistics']['total_seating_plans'] = DB::table('seating_plans')->count();
        
        // Get the distribution of seating plans by room
        $data['statistics']['seating_plans_by_room'] = DB::table('seating_plans')
            ->join('rooms', 'seating_plans.room_id', '=', 'rooms.id')
            ->select('rooms.name', DB::raw('COUNT(seating_plans.id) as count'))
            ->groupBy('rooms.id', 'rooms.name')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
        
        // Get the distribution of seating plans by block
        $data['statistics']['seating_plans_by_block'] = DB::table('seating_plans')
            ->join('rooms', 'seating_plans.room_id', '=', 'rooms.id')
            ->join('blocks', 'rooms.block_id', '=', 'blocks.id')
            ->select('blocks.name', DB::raw('COUNT(seating_plans.id) as count'))
            ->groupBy('blocks.id', 'blocks.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
        
        // Get the distribution of seating plans by course
        $data['statistics']['seating_plans_by_course'] = DB::table('seating_plans')
            ->join('students', 'seating_plans.student_id', '=', 'students.id')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->select('courses.name', DB::raw('COUNT(seating_plans.id) as count'))
            ->groupBy('courses.id', 'courses.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
        
        // Get the distribution of seating plans by date
        $data['statistics']['seating_plans_by_date'] = DB::table('seating_plans')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->toArray();
        
        // Get the recent seating plans
        $data['statistics']['recent_seating_plans'] = DB::table('seating_plans')
            ->select('id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
        
        return $data;
    }

    /**
     * Get question paper data.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getQuestionPaperData(array $parameters = [])
    {
        $data = [
            'title' => 'Question Paper Usage Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'parameters' => $parameters,
            'statistics' => [],
        ];
        
        // Get the total number of question papers
        $data['statistics']['total_question_papers'] = DB::table('question_papers')->count();
        
        // Get the total number of questions
        $data['statistics']['total_questions'] = DB::table('questions')->count();
        
        // Get the distribution of questions by subject
        $data['statistics']['questions_by_subject'] = DB::table('questions')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->join('units', 'topics.unit_id', '=', 'units.id')
            ->join('subjects', 'units.subject_id', '=', 'subjects.id')
            ->select('subjects.name', DB::raw('COUNT(questions.id) as count'))
            ->groupBy('subjects.id', 'subjects.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
        
        // Get the distribution of questions by difficulty level
        $data['statistics']['questions_by_difficulty'] = DB::table('questions')
            ->select('difficulty_level', DB::raw('COUNT(id) as count'))
            ->groupBy('difficulty_level')
            ->orderBy('difficulty_level')
            ->get()
            ->toArray();
        
        // Get the distribution of questions by Bloom's taxonomy level
        $data['statistics']['questions_by_bloom_level'] = DB::table('questions')
            ->join('blooms_taxonomies', 'questions.bloom_level', '=', 'blooms_taxonomies.id')
            ->select('blooms_taxonomies.name', DB::raw('COUNT(questions.id) as count'))
            ->groupBy('blooms_taxonomies.id', 'blooms_taxonomies.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
        
        // Get the recent question papers
        $data['statistics']['recent_question_papers'] = DB::table('question_papers')
            ->select('id', 'title', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
        
        return $data;
    }

    /**
     * Get student performance data.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getStudentPerformanceData(array $parameters = [])
    {
        $data = [
            'title' => 'Student Performance Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'parameters' => $parameters,
            'statistics' => [],
        ];
        
        // This is a placeholder for student performance data
        // In a real application, this would be populated with actual student performance data
        
        $data['statistics']['total_students'] = DB::table('students')->count();
        
        // Get the distribution of students by course
        $data['statistics']['students_by_course'] = DB::table('students')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->select('courses.name', DB::raw('COUNT(students.id) as count'))
            ->groupBy('courses.id', 'courses.name')
            ->orderBy('count', 'desc')
            ->get()
            ->toArray();
        
        // Get the distribution of students by year
        $data['statistics']['students_by_year'] = DB::table('students')
            ->select('year', DB::raw('COUNT(id) as count'))
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->toArray();
        
        // Get the distribution of students by section
        $data['statistics']['students_by_section'] = DB::table('students')
            ->select('section', DB::raw('COUNT(id) as count'))
            ->groupBy('section')
            ->orderBy('section')
            ->get()
            ->toArray();
        
        return $data;
    }

    /**
     * Get custom report data.
     *
     * @param  array  $parameters
     * @return array
     */
    protected function getCustomReportData(array $parameters = [])
    {
        $data = [
            'title' => 'Custom Report',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'parameters' => $parameters,
            'statistics' => [],
        ];
        
        // Custom reports would be implemented based on specific requirements
        // This is a placeholder for custom report data
        
        return $data;
    }

    /**
     * Generate an Excel report.
     *
     * @param  \App\Models\Report  $report
     * @param  array  $data
     * @return array
     */
    protected function generateExcelReport(Report $report, array $data)
    {
        // Create a new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set the report title
        $sheet->setCellValue('A1', $data['title']);
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        
        // Set the generated at timestamp
        $sheet->setCellValue('A2', 'Generated at: ' . $data['generated_at']);
        $sheet->mergeCells('A2:F2');
        
        // Add the report parameters
        $sheet->setCellValue('A4', 'Report Parameters:');
        $sheet->getStyle('A4')->getFont()->setBold(true);
        
        $row = 5;
        foreach ($data['parameters'] as $key => $value) {
            $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $key)));
            $sheet->setCellValue('B' . $row, is_array($value) ? json_encode($value) : $value);
            $row++;
        }
        
        // Add the report statistics
        $row += 2;
        $sheet->setCellValue('A' . $row, 'Report Statistics:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        
        foreach ($data['statistics'] as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $row++;
                $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $key)));
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;
                
                // If it's a simple array of objects, add it as a table
                if (is_array($value) && count($value) > 0 && is_object($value[0])) {
                    // Get the column headers
                    $headers = array_keys(get_object_vars($value[0]));
                    
                    // Add the headers
                    $col = 'A';
                    foreach ($headers as $header) {
                        $sheet->setCellValue($col . $row, ucfirst(str_replace('_', ' ', $header)));
                        $sheet->getStyle($col . $row)->getFont()->setBold(true);
                        $col++;
                    }
                    $row++;
                    
                    // Add the data
                    foreach ($value as $item) {
                        $col = 'A';
                        foreach ($headers as $header) {
                            $sheet->setCellValue($col . $row, $item->$header);
                            $col++;
                        }
                        $row++;
                    }
                }
            } else {
                $sheet->setCellValue('A' . $row, ucfirst(str_replace('_', ' ', $key)));
                $sheet->setCellValue('B' . $row, $value);
                $row++;
            }
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Save the spreadsheet to a file
        $filename = 'report_' . $report->id . '_' . time() . '.xlsx';
        $path = 'reports/' . $filename;
        
        $writer = new Xlsx($spreadsheet);
        $writer->save(storage_path('app/public/' . $path));
        
        return [$path, 'excel'];
    }

    /**
     * Generate a PDF report.
     *
     * @param  \App\Models\Report  $report
     * @param  array  $data
     * @return array
     */
    protected function generatePdfReport(Report $report, array $data)
    {
        // Generate the PDF content
        $html = view('admin.reports.pdf', compact('report', 'data'))->render();
        
        // Save the PDF to a file
        $filename = 'report_' . $report->id . '_' . time() . '.pdf';
        $path = 'reports/' . $filename;
        
        $pdf = app()->make('dompdf.wrapper');
        $pdf->loadHTML($html);
        Storage::put('public/' . $path, $pdf->output());
        
        return [$path, 'pdf'];
    }
}

