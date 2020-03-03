<?php

namespace Bgaze\BladeIndenter;

use Illuminate\Support\Collection;

/**
 * The indenter class
 *
 * @author bgaze <benjamin@bgaze.fr>
 */
class BladeIndenter
{
    /**
     * HTML self closing tags
     *
     * @var array
     */
    protected static $self_closing_tags = [];

    /**
     * Blade self closing directives
     *
     * @var array
     */
    protected static $self_closing_directives = [];

    /**
     * Blade closing directives
     *
     * @var array
     */
    protected static $closing_directives = [];

    /**
     * Blade "else" directives
     *
     * @var array
     */
    protected static $else_directives = [];

    /**
     * Tracks the current indent level.
     *
     * @var int
     */
    protected $indent;

    /**
     * Tracks the current line index.
     *
     * @var int
     */
    protected $line;

    /**
     * Storage for template lines.
     *
     * @var Collection
     */
    protected $lines;


    /**
     * Set self-closing tags.
     *
     * @param  array  $tags
     *
     * @return BladeIndenter
     */
    public function setSelfClosingTags(array $tags)
    {
        self::$self_closing_tags = array_unique($tags);
        return $this;
    }


    /**
     * Add self-closing tags.
     *
     * @param  array  $tags
     *
     * @return BladeIndenter
     */
    public function addSelfClosingTags(array $tags)
    {
        self::$self_closing_tags = array_unique(array_merge(self::$self_closing_tags, $tags));
        return $this;
    }


    /**
     * Set self-closing directives.
     *
     * @param  array  $directives
     *
     * @return BladeIndenter
     */
    public function setSelfClosingDirectives(array $directives)
    {
        self::$self_closing_directives = array_unique($directives);
        return $this;
    }


    /**
     * Add self-closing directives.
     *
     * @param  array  $directives
     *
     * @return BladeIndenter
     */
    public function addSelfClosingDirectives(array $directives)
    {
        self::$self_closing_directives = array_unique(array_merge(self::$self_closing_directives, $directives));
        return $this;
    }


    /**
     * Set closing directives.
     *
     * @param  array  $directives
     *
     * @return BladeIndenter
     */
    public function setClosingDirectives(array $directives)
    {
        self::$closing_directives = $directives;
        return $this;
    }


    /**
     * Add closing directives.
     *
     * @param  array  $directives
     *
     * @return BladeIndenter
     */
    public function addClosingDirectives(array $directives)
    {
        self::$closing_directives = array_merge(self::$closing_directives, $directives);
        return $this;
    }


    /**
     * Set else directives.
     *
     * @param  array  $directives
     *
     * @return BladeIndenter
     */
    public function setElseDirectives(array $directives)
    {
        self::$else_directives = array_unique($directives);
        return $this;
    }


    /**
     * Add else directives.
     *
     * @param  array  $directives
     *
     * @return BladeIndenter
     */
    public function addElseDirectives(array $directives)
    {
        self::$else_directives = array_unique(array_merge(self::$else_directives, $directives));
        return $this;
    }


    /**
     * Format a Blade template string.
     *
     * @param  string  $input  The raw blade template
     *
     * @return string The formatted blade template
     */
    public function indent($input)
    {
        $this->indent = 0;

        $this->lines = collect(explode("\n", trim($input)))->map(function ($line) {
            return trim($line);
        });

        return $this->lines
            ->map(function ($line, $num) {
                $this->line = $num;
                $indent = $this->indentLine($line);
                return str_repeat(' ', $indent * 4) . $line;
            })
            ->implode("\n");
    }


    /**
     * Guess a line indent level.
     *
     * @param  string  $line  The line content
     *
     * @return int The indent level
     */
    protected function indentLine($line)
    {
        if (preg_match('/^<(\/)?([a-zA-Z]+|h[1-6])[^[a-zA-Z1-6]/', $line, $matches)) {
            return $this->indentHtmlLine($line, $matches[2], !empty($matches[1]));
        }

        if (preg_match('/^@([a-zA-Z_]+)/', $line, $matches)) {
            return $this->indentBladeLine($matches[1]);
        }

        return $this->indent;
    }


    /**
     * Guess a HTML line indent level.
     *
     * @param  string  $line  The line content
     * @param  string  $tag  The HTML tag
     * @param  bool  $closes  Is the tag a closing tag
     *
     * @return int The indent level
     */
    protected function indentHtmlLine($line, $tag, $closes)
    {
        if (in_array($tag, self::$self_closing_tags)) {
            return $this->indent;
        }

        if (!$closes && preg_match("/<\/$tag>/", $line)) {
            return $this->indent;
        }

        if ($closes) {
            $this->indent--;
        }

        $indent = $this->indent;

        if (!$closes) {
            $this->indent++;
        }

        return $indent;
    }


    /**
     * Guess a Blade line indent level.
     *
     * @param  string  $directive  The Blade directive
     *
     * @return int The indent level
     */
    protected function indentBladeLine($directive)
    {
        if ($this->elseDirective($directive)) {
            return $this->indent - 1;
        }

        if ($this->closingDirective($directive)) {
            $this->indent--;
            return $this->indent;
        }

        if ($this->selfClosingDirective($directive)) {
            return $this->indent;
        }

        $this->indent++;
        return $this->indent - 1;
    }


    /**
     * Check if the line is a "else" line.
     *
     * @param  string  $directive  The Blade directive
     *
     * @return bool
     */
    protected function elseDirective($directive)
    {
        return (preg_match('/^else/', $directive) || in_array($directive, self::$else_directives));
    }


    /**
     * Check if the line is a closing directive.
     *
     * @param  string  $directive  The Blade directive
     *
     * @return bool
     */
    protected function closingDirective($directive)
    {
        if (substr($directive, 0, 3) === 'end') {
            return true;
        }

        return collect(self::$closing_directives)->flatten()->contains($directive);
    }


    /**
     * Check if the line is a self-closing directive.
     *
     * @param  string  $directive  The Blade directive
     *
     * @return bool
     */
    protected function selfClosingDirective($directive)
    {
        if (in_array($directive, self::$self_closing_directives)) {
            return true;
        }

        return !$this->hasClosingDirective($directive);
    }


    /**
     * Check if the line has a closing directive.
     *
     * @param  string  $directive  The Blade directive
     *
     * @return bool
     */
    protected function hasClosingDirective($directive)
    {
        $openPattern = "/^@{$directive}/";

        if (isset(self::$closing_directives[$directive])) {
            $directives = implode('|', (array) self::$closing_directives[$directive]);
            $closePattern = "/^@(end{$directive}|{$directives})/";
        } else {
            $closePattern = "/^@end{$directive}/";
        }

        $level = 1;

        foreach ($this->lines->slice($this->line + 1) as $l) {
            if (preg_match($openPattern, $l)) {
                $level++;
            }

            if (preg_match($closePattern, $l)) {
                $level--;
            }

            if ($level === 0) {
                return true;
            }
        }

        return false;
    }
}