<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SettingsService
{
    /**
     * Get all settings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSettings()
    {
        return Cache::remember('settings', 60 * 60, function () {
            return Setting::orderBy('group')
                ->orderBy('order')
                ->get();
        });
    }

    /**
     * Get settings by group.
     *
     * @param  string  $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSettingsByGroup($group)
    {
        return Setting::getByGroup($group);
    }

    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getSetting($key, $default = null)
    {
        return Setting::getValue($key, $default);
    }

    /**
     * Set a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  string|null  $type
     * @return \App\Models\Setting|null
     */
    public function setSetting($key, $value, $type = null)
    {
        $result = Setting::setValue($key, $value, $type);
        
        // Clear cache
        Cache::forget('settings');
        
        return $result;
    }

    /**
     * Get all backups.
     *
     * @return array
     */
    public function getBackups()
    {
        $backupPath = storage_path('app/backups');
        
        if (!File::exists($backupPath)) {
            return [];
        }
        
        $files = File::files($backupPath);
        $backups = [];
        
        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $file->getSize(),
                'created_at' => $file->getMTime(),
            ];
        }
        
        // Sort by created_at (newest first)
        usort($backups, function ($a, $b) {
            return $b['created_at'] - $a['created_at'];
        });
        
        return $backups;
    }

    /**
     * Get the backup file path.
     *
     * @param  string  $filename
     * @return string
     */
    public function getBackupPath($filename)
    {
        return storage_path('app/backups/' . $filename);
    }

    /**
     * Delete a backup.
     *
     * @param  string  $filename
     * @return bool
     */
    public function deleteBackup($filename)
    {
        $backupPath = $this->getBackupPath($filename);
        
        if (File::exists($backupPath)) {
            return File::delete($backupPath);
        }
        
        return false;
    }

    /**
     * Get system information.
     *
     * @return array
     */
    public function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database' => config('database.connections.' . config('database.default') . '.driver'),
            'timezone' => config('app.timezone'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug') ? 'Enabled' : 'Disabled',
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'mail_driver' => config('mail.default'),
            'storage_path' => storage_path(),
            'cache_path' => storage_path('framework/cache'),
            'log_path' => storage_path('logs'),
            'app_size' => $this->getDirectorySize(base_path()),
            'storage_size' => $this->getDirectorySize(storage_path()),
            'database_size' => $this->getDatabaseSize(),
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'Unknown',
            'client_ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
        ];
    }

    /**
     * Get the size of a directory.
     *
     * @param  string  $path
     * @return string
     */
    protected function getDirectorySize($path)
    {
        $size = 0;
        
        foreach (File::allFiles($path) as $file) {
            $size += $file->getSize();
        }
        
        return $this->formatBytes($size);
    }

    /**
     * Get the database size.
     *
     * @return string
     */
    protected function getDatabaseSize()
    {
        try {
            $databaseName = config('database.connections.' . config('database.default') . '.database');
            $result = \DB::select('SELECT SUM(data_length + index_length) AS size FROM information_schema.TABLES WHERE table_schema = ?', [$databaseName]);
            
            if (isset($result[0]->size)) {
                return $this->formatBytes($result[0]->size);
            }
        } catch (\Exception $e) {
            // Ignore
        }
        
        return 'Unknown';
    }

    /**
     * Format bytes to human-readable format.
     *
     * @param  int  $bytes
     * @param  int  $precision
     * @return string
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Send a test email.
     *
     * @param  string  $email
     * @return bool
     */
    public function sendTestEmail($email)
    {
        try {
            Mail::raw('This is a test email from the Exam Seat Management System.', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email from Exam Seat Management System');
            });
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get available themes.
     *
     * @return array
     */
    public function getAvailableThemes()
    {
        return [
            'default' => 'Default (Navy Blue)',
            'dark' => 'Dark Mode',
            'light' => 'Light Mode',
            'blue' => 'Blue Theme',
            'green' => 'Green Theme',
            'purple' => 'Purple Theme',
            'red' => 'Red Theme',
            'orange' => 'Orange Theme',
            'custom' => 'Custom Theme',
        ];
    }

    /**
     * Generate theme CSS.
     *
     * @param  string  $primaryColor
     * @param  string  $secondaryColor
     * @param  string  $customCSS
     * @return bool
     */
    public function generateThemeCSS($primaryColor, $secondaryColor, $customCSS = null)
    {
        $css = <<<CSS
        :root {
            --primary-color: {$primaryColor};
            --secondary-color: {$secondaryColor};
        }
        
        /* Primary color styles */
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: darken(var(--primary-color), 10%);
            border-color: darken(var(--primary-color), 10%);
        }
        
        /* Secondary color styles */
        .bg-secondary {
            background-color: var(--secondary-color) !important;
        }
        
        .text-secondary {
            color: var(--secondary-color) !important;
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-secondary:hover {
            background-color: darken(var(--secondary-color), 10%);
            border-color: darken(var(--secondary-color), 10%);
        }
        
        /* Custom CSS */
        {$customCSS}
        CSS;
        
        try {
            Storage::disk('public')->put('css/theme.css', $css);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Initialize default settings.
     *
     * @return void
     */
    public function initializeDefaultSettings()
    {
        $defaultSettings = [
            // General settings
            [
                'key' => 'app_name',
                'value' => 'Exam Seat Management System',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Application Name',
                'description' => 'The name of the application',
                'is_public' => true,
                'is_system' => true,
                'order' => 1,
            ],
            [
                'key' => 'app_description',
                'value' => 'A comprehensive system for managing examination seating arrangements and question papers.',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Application Description',
                'description' => 'A short description of the application',
                'is_public' => true,
                'is_system' => true,
                'order' => 2,
            ],
            [
                'key' => 'app_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'general',
                'label' => 'Application Logo',
                'description' => 'The logo of the application',
                'is_public' => true,
                'is_system' => true,
                'order' => 3,
            ],
            [
                'key' => 'app_favicon',
                'value' => null,
                'type' => 'image',
                'group' => 'general',
                'label' => 'Application Favicon',
                'description' => 'The favicon of the application',
                'is_public' => true,
                'is_system' => true,
                'order' => 4,
            ],
            [
                'key' => 'institution_name',
                'value' => 'Kalvi ERP',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Institution Name',
                'description' => 'The name of the institution',
                'is_public' => true,
                'is_system' => false,
                'order' => 5,
            ],
            [
                'key' => 'institution_address',
                'value' => '',
                'type' => 'text',
                'group' => 'general',
                'label' => 'Institution Address',
                'description' => 'The address of the institution',
                'is_public' => true,
                'is_system' => false,
                'order' => 6,
            ],
            [
                'key' => 'institution_phone',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Institution Phone',
                'description' => 'The phone number of the institution',
                'is_public' => true,
                'is_system' => false,
                'order' => 7,
            ],
            [
                'key' => 'institution_email',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Institution Email',
                'description' => 'The email address of the institution',
                'is_public' => true,
                'is_system' => false,
                'order' => 8,
            ],
            [
                'key' => 'institution_website',
                'value' => '',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Institution Website',
                'description' => 'The website of the institution',
                'is_public' => true,
                'is_system' => false,
                'order' => 9,
            ],
            [
                'key' => 'institution_logo',
                'value' => null,
                'type' => 'image',
                'group' => 'general',
                'label' => 'Institution Logo',
                'description' => 'The logo of the institution',
                'is_public' => true,
                'is_system' => false,
                'order' => 10,
            ],
            
            // Appearance settings
            [
                'key' => 'app_theme',
                'value' => 'default',
                'type' => 'select',
                'group' => 'appearance',
                'label' => 'Application Theme',
                'description' => 'The theme of the application',
                'is_public' => true,
                'is_system' => true,
                'order' => 1,
                'options' => [
                    'default' => 'Default (Navy Blue)',
                    'dark' => 'Dark Mode',
                    'light' => 'Light Mode',
                    'blue' => 'Blue Theme',
                    'green' => 'Green Theme',
                    'purple' => 'Purple Theme',
                    'red' => 'Red Theme',
                    'orange' => 'Orange Theme',
                    'custom' => 'Custom Theme',
                ],
            ],
            [
                'key' => 'primary_color',
                'value' => '#000080',
                'type' => 'color',
                'group' => 'appearance',
                'label' => 'Primary Color',
                'description' => 'The primary color of the application',
                'is_public' => true,
                'is_system' => true,
                'order' => 2,
            ],
            [
                'key' => 'secondary_color',
                'value' => '#4a5568',
                'type' => 'color',
                'group' => 'appearance',
                'label' => 'Secondary Color',
                'description' => 'The secondary color of the application',
                'is_public' => true,
                'is_system' => true,
                'order' => 3,
            ],
            [
                'key' => 'custom_css',
                'value' => '',
                'type' => 'text',
                'group' => 'appearance',
                'label' => 'Custom CSS',
                'description' => 'Custom CSS for the application',
                'is_public' => false,
                'is_system' => true,
                'order' => 4,
            ],
            [
                'key' => 'show_footer',
                'value' => true,
                'type' => 'boolean',
                'group' => 'appearance',
                'label' => 'Show Footer',
                'description' => 'Whether to show the footer',
                'is_public' => true,
                'is_system' => false,
                'order' => 5,
            ],
            [
                'key' => 'footer_text',
                'value' => '© ' . date('Y') . ' Exam Seat Management System. All rights reserved.',
                'type' => 'text',
                'group' => 'appearance',
                'label' => 'Footer Text',
                'description' => 'The text to display in the footer',
                'is_public' => true,
                'is_system' => false,
                'order' => 6,
            ],
            
            // Email settings
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@example.com',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Mail From Address',
                'description' => 'The email address to send emails from',
                'is_public' => false,
                'is_system' => true,
                'order' => 1,
            ],
            [
                'key' => 'mail_from_name',
                'value' => 'Exam Seat Management System',
                'type' => 'string',
                'group' => 'email',
                'label' => 'Mail From Name',
                'description' => 'The name to send emails from',
                'is_public' => false,
                'is_system' => true,
                'order' => 2,
            ],
            [
                'key' => 'mail_footer_text',
                'value' => 'This email was sent from the Exam Seat Management System.',
                'type' => 'text',
                'group' => 'email',
                'label' => 'Mail Footer Text',
                'description' => 'The text to display in the footer of emails',
                'is_public' => false,
                'is_system' => false,
                'order' => 3,
            ],
            
            // Security settings
            [
                'key' => 'login_attempts',
                'value' => 5,
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Login Attempts',
                'description' => 'The number of login attempts before lockout',
                'is_public' => false,
                'is_system' => true,
                'order' => 1,
            ],
            [
                'key' => 'lockout_time',
                'value' => 10,
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Lockout Time',
                'description' => 'The lockout time in minutes',
                'is_public' => false,
                'is_system' => true,
                'order' => 2,
            ],
            [
                'key' => 'password_expiry',
                'value' => 90,
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Password Expiry',
                'description' => 'The number of days before password expiry',
                'is_public' => false,
                'is_system' => true,
                'order' => 3,
            ],
            [
                'key' => 'password_history',
                'value' => 3,
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Password History',
                'description' => 'The number of previous passwords to remember',
                'is_public' => false,
                'is_system' => true,
                'order' => 4,
            ],
            [
                'key' => 'session_timeout',
                'value' => 120,
                'type' => 'integer',
                'group' => 'security',
                'label' => 'Session Timeout',
                'description' => 'The session timeout in minutes',
                'is_public' => false,
                'is_system' => true,
                'order' => 5,
            ],
            
            // Notification settings
            [
                'key' => 'enable_email_notifications',
                'value' => true,
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Enable Email Notifications',
                'description' => 'Whether to enable email notifications',
                'is_public' => false,
                'is_system' => false,
                'order' => 1,
            ],
            [
                'key' => 'enable_browser_notifications',
                'value' => true,
                'type' => 'boolean',
                'group' => 'notifications',
                'label' => 'Enable Browser Notifications',
                'description' => 'Whether to enable browser notifications',
                'is_public' => true,
                'is_system' => false,
                'order' => 2,
            ],
            [
                'key' => 'notification_email_subject',
                'value' => 'New Notification from Exam Seat Management System',
                'type' => 'string',
                'group' => 'notifications',
                'label' => 'Notification Email Subject',
                'description' => 'The subject of notification emails',
                'is_public' => false,
                'is_system' => false,
                'order' => 3,
            ],
            
            // Seating plan settings
            [
                'key' => 'default_allocation_strategy',
                'value' => 'random',
                'type' => 'select',
                'group' => 'seating_plans',
                'label' => 'Default Allocation Strategy',
                'description' => 'The default strategy for allocating seats',
                'is_public' => false,
                'is_system' => false,
                'order' => 1,
                'options' => [
                    'random' => 'Random Allocation',
                    'sequential' => 'Sequential Allocation',
                    'alternate_course' => 'Alternate Course Allocation',
                    'mixed' => 'Mixed Allocation',
                ],
            ],
            [
                'key' => 'show_room_layout',
                'value' => true,
                'type' => 'boolean',
                'group' => 'seating_plans',
                'label' => 'Show Room Layout',
                'description' => 'Whether to show the room layout in seating plans',
                'is_public' => false,
                'is_system' => false,
                'order' => 2,
            ],
            [
                'key' => 'enable_student_notifications',
                'value' => true,
                'type' => 'boolean',
                'group' => 'seating_plans',
                'label' => 'Enable Student Notifications',
                'description' => 'Whether to notify students about their seating arrangements',
                'is_public' => false,
                'is_system' => false,
                'order' => 3,
            ],
            
            // Question paper settings
            [
                'key' => 'default_question_paper_header',
                'value' => 'Exam Seat Management System',
                'type' => 'string',
                'group' => 'question_papers',
                'label' => 'Default Question Paper Header',
                'description' => 'The default header for question papers',
                'is_public' => false,
                'is_system' => false,
                'order' => 1,
            ],
            [
                'key' => 'default_question_paper_footer',
                'value' => 'End of Question Paper',
                'type' => 'string',
                'group' => 'question_papers',
                'label' => 'Default Question Paper Footer',
                'description' => 'The default footer for question papers',
                'is_public' => false,
                'is_system' => false,
                'order' => 2,
            ],
            [
                'key' => 'show_answer_key',
                'value' => false,
                'type' => 'boolean',
                'group' => 'question_papers',
                'label' => 'Show Answer Key',
                'description' => 'Whether to show the answer key in question papers',
                'is_public' => false,
                'is_system' => false,
                'order' => 3,
            ],
            [
                'key' => 'show_marking_scheme',
                'value' => true,
                'type' => 'boolean',
                'group' => 'question_papers',
                'label' => 'Show Marking Scheme',
                'description' => 'Whether to show the marking scheme in question papers',
                'is_public' => false,
                'is_system' => false,
                'order' => 4,
            ],
            
            // Backup settings
            [
                'key' => 'enable_auto_backup',
                'value' => true,
                'type' => 'boolean',
                'group' => 'backup',
                'label' => 'Enable Auto Backup',
                'description' => 'Whether to enable automatic backups',
                'is_public' => false,
                'is_system' => true,
                'order' => 1,
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'select',
                'group' => 'backup',
                'label' => 'Backup Frequency',
                'description' => 'The frequency of automatic backups',
                'is_public' => false,
                'is_system' => true,
                'order' => 2,
                'options' => [
                    'hourly' => 'Hourly',
                    'daily' => 'Daily',
                    'weekly' => 'Weekly',
                    'monthly' => 'Monthly',
                ],
            ],
            [
                'key' => 'backup_retention',
                'value' => 7,
                'type' => 'integer',
                'group' => 'backup',
                'label' => 'Backup Retention',
                'description' => 'The number of days to retain backups',
                'is_public' => false,
                'is_system' => true,
                'order' => 3,
            ],
            
            // System settings
            [
                'key' => 'app_debug',
                'value' => false,
                'type' => 'boolean',
                'group' => 'system',
                'label' => 'App Debug',
                'description' => 'Whether to enable debug mode',
                'is_public' => false,
                'is_system' => true,
                'order' => 1,
            ],
            [
                'key' => 'app_env',
                'value' => 'production',
                'type' => 'select',
                'group' => 'system',
                'label' => 'App Environment',
                'description' => 'The application environment',
                'is_public' => false,
                'is_system' => true,
                'order' => 2,
                'options' => [
                    'local' => 'Local',
                    'development' => 'Development',
                    'staging' => 'Staging',
                    'production' => 'Production',
                ],
            ],
            [
                'key' => 'app_timezone',
                'value' => 'UTC',
                'type' => 'string',
                'group' => 'system',
                'label' => 'App Timezone',
                'description' => 'The application timezone',
                'is_public' => false,
                'is_system' => true,
                'order' => 3,
            ],
            [
                'key' => 'app_locale',
                'value' => 'en',
                'type' => 'string',
                'group' => 'system',
                'label' => 'App Locale',
                'description' => 'The application locale',
                'is_public' => false,
                'is_system' => true,
                'order' => 4,
            ],
            [
                'key' => 'app_url',
                'value' => 'http://localhost',
                'type' => 'string',
                'group' => 'system',
                'label' => 'App URL',
                'description' => 'The application URL',
                'is_public' => false,
                'is_system' => true,
                'order' => 5,
            ],
        ];
        
        foreach ($defaultSettings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}

