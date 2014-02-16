<?php namespace Console;

class Progressbar
{

    private $color = '32';
    private $blocks = 20;
    private $current_element = 0;
    private $max_elements;

    public function __construct($max_elements)
    {
        $this->max_elements = $max_elements;
        $this->setProgress(0);
    }

    public function increase($amount = 1)
    {

        $this->current_element += $amount;
        $percent = ($this->current_element / $this->max_elements) * 100;
        $this->setProgress($percent);

    }

    private function setProgress($percent)
    {

        $blocks = $this->calculateBlocks($percent);

        $string = $this->renderBar($blocks) . $this->renderText($percent);

        $end_of_line = ($percent === 100) ? "\n" : "\r";

        Console::info($string, $end_of_line);

    }

    private function calculateBlocks($percent)
    {
        return floor($percent * ($this->blocks / 100));
    }

    private function renderText($percent)
    {
        return ' ' . floor($percent) . '% (' . $this->current_element . '/' . $this->max_elements . ')';
    }

    private function renderBar($blocks)
    {

        $done = Console::colorString($this->color, str_repeat('#', $blocks));
        $not_done = str_repeat(' ', $this->blocks - $blocks);

        return 'Progress: [' . $done . $not_done . ']';

    }

}