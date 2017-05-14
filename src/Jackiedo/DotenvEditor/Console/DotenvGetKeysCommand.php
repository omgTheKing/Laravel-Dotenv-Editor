<?php  namespace Jackiedo\DotenvEditor\Console;

use Illuminate\Console\Command;
use Jackiedo\DotenvEditor\DotenvEditor;

class DotenvGetKeysCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dotenv:get-keys
                            {--filepath= : The file path should use to load for working. Do not use if you want to load file .env at root application folder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all setter in the .env file';

    /**
     * The .env file editor instance
     *
     * @var \Jackiedo\DotenvEditor\DotenvEditor
     */
    protected $editor;

    /**
     * The .env file path
     *
     * @var string|null
     */
    protected $filePath = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(DotenvEditor $editor)
    {
        parent::__construct();

        $this->editor = $editor;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filePath       = $this->stringToType($this->option('filepath'));
        $this->filePath = (is_string($filePath)) ? base_path($filePath) : null;

        $allKeys = $this->editor->load($this->filePath)->getKeys();
        $output = [];
        foreach ($allKeys as $key => $info) {
            $data = [
                'key'     => $key,
                'export'  => ($info['export']) ? 'true' : 'false',
                'value'   => $info['value'],
                'comment' => $info['comment'],
                'line'    => $info['line']
            ];
            $output[] = $data;
        }

        $total   = count($output);

        $headers = ['Key', 'Use export', 'Value', 'Comment', 'In line'];

        $this->line('Loading keys in your file...');
        $this->line('');
        $this->table($headers, $output);
        $this->line('');
        $this->info("You have total {$total} key in your file");
    }

    /**
     * Convert string to corresponding type
     *
     * @param  string $string
     *
     * @return mixed
     */
    protected function stringToType($string) {
        if (is_string($string)) {
            switch (true) {
                case ($string == 'null' || $string == 'NULL'):
                    $string = null;
                    break;

                case ($string == 'true' || $string == 'TRUE'):
                    $string = true;
                    break;

                case ($string == 'false' || $string == 'FALSE'):
                    $string = false;
                    break;

                default:
                    break;
            }
        }

        return $string;
    }
}
