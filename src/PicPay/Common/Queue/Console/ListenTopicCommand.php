<?php


namespace PicPay\PicPay\Common\Queue\Console;

use Illuminate\Console\Command;
use Illuminate\Queue\Listener;
use Illuminate\Queue\ListenerOptions;

/**
 * Class ListenTopicCommand
 * @package PicPay\PicPay\Common\Queue\Console
 */
class ListenTopicCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'topic:listen
                            {--topic= : The name of the topic to work} 
                            {--group-id= : The name of the work group} 
                            {--metadata-broker-list= : The host of the kafka broker} 
                            {--enable-auto-commit= : Enable auto commit events}
                            {--auto-offset-reset= : Set offset configuration}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to a given topic';
    /**
     * The queue listener instance.
     *
     * @var \Illuminate\Queue\Listener
     */
    protected $listener;

    private static $connection = 'rdkafka';

    /**
     * Create a new queue listen command.
     *
     * @param  \Illuminate\Queue\Listener  $listener
     * @return void
     */
    public function __construct(Listener $listener)
    {
        parent::__construct();
        $this->setOutputHandler($this->listener = $listener);
    }
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->listener->listen(
            self::$connection, $this->getQueue(), $this->gatherOptions()
        );
    }
    /**
     * Get the name of the queue connection to listen on.
     *
     * @return string
     */
    protected function getQueue()
    {
        //topic
        //group.id
        //metadata.broker.list
        //enable.auto.commit
        //auto.offset.reset

        return $this->input->getOption('topic') ?: $this->laravel['config']->get(
            sprintf("queue.connections.%s.topic", self::$connection), 'default'
        );
    }
    /**
     * Get the listener options for the command.
     *
     * @return \Illuminate\Queue\ListenerOptions
     */
    protected function gatherOptions()
    {
        return new ListenerOptions(
            $this->option('env'), $this->option('delay'),
            $this->option('memory'), $this->option('timeout'),
            $this->option('sleep'), $this->option('tries'),
            $this->option('force')
        );
    }
    /**
     * Set the options on the queue listener.
     *
     * @param  \Illuminate\Queue\Listener  $listener
     * @return void
     */
    protected function setOutputHandler(Listener $listener)
    {
        $listener->setOutputHandler(function ($type, $line) {
            $this->output->write($line);
        });
    }
}