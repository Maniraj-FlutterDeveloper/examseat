<?php

namespace App\Services;

use App\Models\DashboardWidget;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get the data for a dashboard widget.
     *
     * @param  \App\Models\DashboardWidget  $widget
     * @return array
     */
    public function getWidgetData(DashboardWidget $widget)
    {
        switch ($widget->type) {
            case 'chart':
                return $this->getChartData($widget);
            case 'table':
                return $this->getTableData($widget);
            case 'metric':
                return $this->getMetricData($widget);
            case 'list':
                return $this->getListData($widget);
            case 'map':
                return $this->getMapData($widget);
            case 'custom':
                return $this->getCustomData($widget);
            default:
                return [
                    'error' => 'Unsupported widget type: ' . $widget->type,
                ];
        }
    }

    /**
     * Get the data for a chart widget.
     *
     * @param  \App\Models\DashboardWidget  $widget
     * @return array
     */
    protected function getChartData(DashboardWidget $widget)
    {
        $config = $widget->config;
        $dataSource = $config['data_source'] ?? null;
        
        if (!$dataSource) {
            return [
                'error' => 'No data source specified for chart widget.',
            ];
        }
        
        switch ($dataSource) {
            case 'students_by_course':
                return $this->getStudentsByCourseData();
            case 'rooms_by_block':
                return $this->getRoomsByBlockData();
            case 'questions_by_subject':
                return $this->getQuestionsBySubjectData();
            case 'questions_by_difficulty':
                return $this->getQuestionsByDifficultyData();
            case 'questions_by_bloom_level':
                return $this->getQuestionsByBloomLevelData();
            case 'seating_plans_by_date':
                return $this->getSeatingPlansByDateData();
            default:
                return [
                    'error' => 'Unsupported data source: ' . $dataSource,
                ];
        }
    }

    /**
     * Get the data for a table widget.
     *
     * @param  \App\Models\DashboardWidget  $widget
     * @return array
     */
    protected function getTableData(DashboardWidget $widget)
    {
        $config = $widget->config;
        $dataSource = $config['data_source'] ?? null;
        
        if (!$dataSource) {
            return [
                'error' => 'No data source specified for table widget.',
            ];
        }
        
        switch ($dataSource) {
            case 'recent_students':
                return $this->getRecentStudentsData();
            case 'recent_rooms':
                return $this->getRecentRoomsData();
            case 'recent_seating_plans':
                return $this->getRecentSeatingPlansData();
            case 'recent_question_papers':
                return $this->getRecentQuestionPapersData();
            default:
                return [
                    'error' => 'Unsupported data source: ' . $dataSource,
                ];
        }
    }

    /**
     * Get the data for a metric widget.
     *
     * @param  \App\Models\DashboardWidget  $widget
     * @return array
     */
    protected function getMetricData(DashboardWidget $widget)
    {
        $config = $widget->config;
        $metricType = $config['metric_type'] ?? null;
        
        if (!$metricType) {
            return [
                'error' => 'No metric type specified for metric widget.',
            ];
        }
        
        switch ($metricType) {
            case 'total_students':
                return [
                    'value' => DB::table('students')->count(),
                    'label' => 'Total Students',
                    'icon' => $config['icon'] ?? 'user-graduate',
                    'color' => $config['color'] ?? 'primary',
                ];
            case 'total_rooms':
                return [
                    'value' => DB::table('rooms')->count(),
                    'label' => 'Total Rooms',
                    'icon' => $config['icon'] ?? 'door-open',
                    'color' => $config['color'] ?? 'success',
                ];
            case 'total_blocks':
                return [
                    'value' => DB::table('blocks')->count(),
                    'label' => 'Total Blocks',
                    'icon' => $config['icon'] ?? 'building',
                    'color' => $config['color'] ?? 'info',
                ];
            case 'total_courses':
                return [
                    'value' => DB::table('courses')->count(),
                    'label' => 'Total Courses',
                    'icon' => $config['icon'] ?? 'graduation-cap',
                    'color' => $config['color'] ?? 'warning',
                ];
            case 'total_seating_plans':
                return [
                    'value' => DB::table('seating_plans')->count(),
                    'label' => 'Total Seating Plans',
                    'icon' => $config['icon'] ?? 'chair',
                    'color' => $config['color'] ?? 'danger',
                ];
            case 'total_questions':
                return [
                    'value' => DB::table('questions')->count(),
                    'label' => 'Total Questions',
                    'icon' => $config['icon'] ?? 'question-circle',
                    'color' => $config['color'] ?? 'secondary',
                ];
            case 'total_question_papers':
                return [
                    'value' => DB::table('question_papers')->count(),
                    'label' => 'Total Question Papers',
                    'icon' => $config['icon'] ?? 'file-alt',
                    'color' => $config['color'] ?? 'dark',
                ];
            default:
                return [
                    'error' => 'Unsupported metric type: ' . $metricType,
                ];
        }
    }

    /**
     * Get the data for a list widget.
     *
     * @param  \App\Models\DashboardWidget  $widget
     * @return array
     */
    protected function getListData(DashboardWidget $widget)
    {
        $config = $widget->config;
        $dataSource = $config['data_source'] ?? null;
        
        if (!$dataSource) {
            return [
                'error' => 'No data source specified for list widget.',
            ];
        }
        
        switch ($dataSource) {
            case 'recent_students':
                return $this->getRecentStudentsData(true);
            case 'recent_rooms':
                return $this->getRecentRoomsData(true);
            case 'recent_seating_plans':
                return $this->getRecentSeatingPlansData(true);
            case 'recent_question_papers':
                return $this->getRecentQuestionPapersData(true);
            default:
                return [
                    'error' => 'Unsupported data source: ' . $dataSource,
                ];
        }
    }

    /**
     * Get the data for a map widget.
     *
     * @param  \App\Models\DashboardWidget  $widget
     * @return array
     */
    protected function getMapData(DashboardWidget $widget)
    {
        // Map widgets would be implemented based on specific requirements
        // This is a placeholder for map widget data
        
        return [
            'error' => 'Map widgets are not implemented yet.',
        ];
    }

    /**
     * Get the data for a custom widget.
     *
     * @param  \App\Models\DashboardWidget  $widget
     * @return array
     */
    protected function getCustomData(DashboardWidget $widget)
    {
        // Custom widgets would be implemented based on specific requirements
        // This is a placeholder for custom widget data
        
        return [
            'error' => 'Custom widgets are not implemented yet.',
        ];
    }

    /**
     * Get the students by course data.
     *
     * @return array
     */
    protected function getStudentsByCourseData()
    {
        $data = DB::table('students')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->select('courses.name', DB::raw('COUNT(students.id) as count'))
            ->groupBy('courses.id', 'courses.name')
            ->orderBy('count', 'desc')
            ->get();
        
        $labels = $data->pluck('name')->toArray();
        $values = $data->pluck('count')->toArray();
        
        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $values,
                        'backgroundColor' => $this->getRandomColors(count($values)),
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Students by Course',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the rooms by block data.
     *
     * @return array
     */
    protected function getRoomsByBlockData()
    {
        $data = DB::table('rooms')
            ->join('blocks', 'rooms.block_id', '=', 'blocks.id')
            ->select('blocks.name', DB::raw('COUNT(rooms.id) as count'))
            ->groupBy('blocks.id', 'blocks.name')
            ->orderBy('count', 'desc')
            ->get();
        
        $labels = $data->pluck('name')->toArray();
        $values = $data->pluck('count')->toArray();
        
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Rooms',
                        'data' => $values,
                        'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                        'borderColor' => 'rgba(54, 162, 235, 1)',
                        'borderWidth' => 1,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Rooms by Block',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the questions by subject data.
     *
     * @return array
     */
    protected function getQuestionsBySubjectData()
    {
        $data = DB::table('questions')
            ->join('topics', 'questions.topic_id', '=', 'topics.id')
            ->join('units', 'topics.unit_id', '=', 'units.id')
            ->join('subjects', 'units.subject_id', '=', 'subjects.id')
            ->select('subjects.name', DB::raw('COUNT(questions.id) as count'))
            ->groupBy('subjects.id', 'subjects.name')
            ->orderBy('count', 'desc')
            ->get();
        
        $labels = $data->pluck('name')->toArray();
        $values = $data->pluck('count')->toArray();
        
        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $values,
                        'backgroundColor' => $this->getRandomColors(count($values)),
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Questions by Subject',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the questions by difficulty data.
     *
     * @return array
     */
    protected function getQuestionsByDifficultyData()
    {
        $data = DB::table('questions')
            ->select('difficulty_level', DB::raw('COUNT(id) as count'))
            ->groupBy('difficulty_level')
            ->orderBy('difficulty_level')
            ->get();
        
        $labels = $data->pluck('difficulty_level')->toArray();
        $values = $data->pluck('count')->toArray();
        
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Questions',
                        'data' => $values,
                        'backgroundColor' => [
                            'rgba(75, 192, 192, 0.5)',
                            'rgba(255, 206, 86, 0.5)',
                            'rgba(255, 99, 132, 0.5)',
                        ],
                        'borderColor' => [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 99, 132, 1)',
                        ],
                        'borderWidth' => 1,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
                'plugins' => [
                    'legend' => [
                        'display' => false,
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Questions by Difficulty Level',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the questions by Bloom's taxonomy level data.
     *
     * @return array
     */
    protected function getQuestionsByBloomLevelData()
    {
        $data = DB::table('questions')
            ->join('blooms_taxonomies', 'questions.bloom_level', '=', 'blooms_taxonomies.id')
            ->select('blooms_taxonomies.name', DB::raw('COUNT(questions.id) as count'))
            ->groupBy('blooms_taxonomies.id', 'blooms_taxonomies.name')
            ->orderBy('count', 'desc')
            ->get();
        
        $labels = $data->pluck('name')->toArray();
        $values = $data->pluck('count')->toArray();
        
        return [
            'type' => 'polarArea',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $values,
                        'backgroundColor' => $this->getRandomColors(count($values)),
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Questions by Bloom\'s Taxonomy Level',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the seating plans by date data.
     *
     * @return array
     */
    protected function getSeatingPlansByDateData()
    {
        $data = DB::table('seating_plans')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(id) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->limit(30)
            ->get();
        
        $labels = $data->pluck('date')->toArray();
        $values = $data->pluck('count')->toArray();
        
        return [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Seating Plans',
                        'data' => $values,
                        'fill' => false,
                        'borderColor' => 'rgba(153, 102, 255, 1)',
                        'tension' => 0.1,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Seating Plans by Date',
                    ],
                ],
            ],
        ];
    }

    /**
     * Get the recent students data.
     *
     * @param  bool  $asList
     * @return array
     */
    protected function getRecentStudentsData($asList = false)
    {
        $students = DB::table('students')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->select('students.id', 'students.name', 'students.roll_number', 'courses.name as course', 'students.created_at')
            ->orderBy('students.created_at', 'desc')
            ->limit(10)
            ->get();
        
        if ($asList) {
            return [
                'items' => $students->map(function ($student) {
                    return [
                        'title' => $student->name,
                        'subtitle' => $student->roll_number,
                        'description' => $student->course,
                        'timestamp' => $student->created_at,
                        'link' => route('admin.students.show', $student->id),
                    ];
                })->toArray(),
            ];
        }
        
        return [
            'headers' => ['ID', 'Name', 'Roll Number', 'Course', 'Created At'],
            'rows' => $students->map(function ($student) {
                return [
                    $student->id,
                    $student->name,
                    $student->roll_number,
                    $student->course,
                    $student->created_at,
                ];
            })->toArray(),
        ];
    }

    /**
     * Get the recent rooms data.
     *
     * @param  bool  $asList
     * @return array
     */
    protected function getRecentRoomsData($asList = false)
    {
        $rooms = DB::table('rooms')
            ->join('blocks', 'rooms.block_id', '=', 'blocks.id')
            ->select('rooms.id', 'rooms.name', 'rooms.capacity', 'blocks.name as block', 'rooms.created_at')
            ->orderBy('rooms.created_at', 'desc')
            ->limit(10)
            ->get();
        
        if ($asList) {
            return [
                'items' => $rooms->map(function ($room) {
                    return [
                        'title' => $room->name,
                        'subtitle' => 'Capacity: ' . $room->capacity,
                        'description' => 'Block: ' . $room->block,
                        'timestamp' => $room->created_at,
                        'link' => route('admin.rooms.show', $room->id),
                    ];
                })->toArray(),
            ];
        }
        
        return [
            'headers' => ['ID', 'Name', 'Capacity', 'Block', 'Created At'],
            'rows' => $rooms->map(function ($room) {
                return [
                    $room->id,
                    $room->name,
                    $room->capacity,
                    $room->block,
                    $room->created_at,
                ];
            })->toArray(),
        ];
    }

    /**
     * Get the recent seating plans data.
     *
     * @param  bool  $asList
     * @return array
     */
    protected function getRecentSeatingPlansData($asList = false)
    {
        $seatingPlans = DB::table('seating_plans')
            ->select('id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        if ($asList) {
            return [
                'items' => $seatingPlans->map(function ($plan) {
                    return [
                        'title' => 'Seating Plan #' . $plan->id,
                        'subtitle' => 'Created: ' . $plan->created_at,
                        'description' => '',
                        'timestamp' => $plan->created_at,
                        'link' => route('admin.seating_plans.show', $plan->id),
                    ];
                })->toArray(),
            ];
        }
        
        return [
            'headers' => ['ID', 'Created At'],
            'rows' => $seatingPlans->map(function ($plan) {
                return [
                    $plan->id,
                    $plan->created_at,
                ];
            })->toArray(),
        ];
    }

    /**
     * Get the recent question papers data.
     *
     * @param  bool  $asList
     * @return array
     */
    protected function getRecentQuestionPapersData($asList = false)
    {
        $questionPapers = DB::table('question_papers')
            ->select('id', 'title', 'created_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        if ($asList) {
            return [
                'items' => $questionPapers->map(function ($paper) {
                    return [
                        'title' => $paper->title,
                        'subtitle' => 'Created: ' . $paper->created_at,
                        'description' => '',
                        'timestamp' => $paper->created_at,
                        'link' => route('admin.question_papers.show', $paper->id),
                    ];
                })->toArray(),
            ];
        }
        
        return [
            'headers' => ['ID', 'Title', 'Created At'],
            'rows' => $questionPapers->map(function ($paper) {
                return [
                    $paper->id,
                    $paper->title,
                    $paper->created_at,
                ];
            })->toArray(),
        ];
    }

    /**
     * Get random colors for charts.
     *
     * @param  int  $count
     * @return array
     */
    protected function getRandomColors($count)
    {
        $colors = [
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(255, 206, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(255, 159, 64, 0.5)',
            'rgba(199, 199, 199, 0.5)',
            'rgba(83, 102, 255, 0.5)',
            'rgba(40, 159, 64, 0.5)',
            'rgba(210, 199, 199, 0.5)',
        ];
        
        // If we need more colors than we have, generate random ones
        if ($count > count($colors)) {
            for ($i = count($colors); $i < $count; $i++) {
                $r = rand(0, 255);
                $g = rand(0, 255);
                $b = rand(0, 255);
                $colors[] = "rgba({$r}, {$g}, {$b}, 0.5)";
            }
        }
        
        return array_slice($colors, 0, $count);
    }
}

