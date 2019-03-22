<?php

namespace Valkyrie;

/**
 * Class RequireHandler
 * Handles and dispatches includes
 */
class RequireHandler{

    /**
     * List of all required files with their groupings
     * @var string[][] Associative array containing groups which their files
     */
    private $list = [];

    /**
     * List of all existing groups for this RequireHandler
     * @var string[] List of strings
     */
    private $groups = [];

    /**
     * Adds Files to the RequireHandler
     * @param string $pattern The glob-pattern for the file-include
     * @param string $group Group of files, "default" is the default group
     */
    public function addFile(string $pattern, string $group = 'default'): void{
        if (!$this->hasGroup($group)) {
            $this->list[$group] = [];
            array_push($this->groups, $group);
        }
        $files = glob($pattern);
        $this->list[$group] = array_merge($this->list[$group], $files);
    }

    /**
     * Returns an array of all group-names
     * @return array A list of all group-names
     */
    public function getGroups(): array{
        return $this->groups;
    }

    /**
     * Returns the full list of groups and files as an associative array
     * @return array The full require-list
     */
    public function getList(): array{
        return $this->list;
    }

    /**
     * Returns all files from the given group
     * @param string $group group-name to retrieve the files from "default" is the default value
     * @return array List of files from the given group
     * @throws \Exception If the requested group does not exist.
     */
    public function getGroup(string $group = 'default'): array{
        if (!$this->hasGroup($group)) {
            throw new \Exception(sprintf("Group %s does not exist.", $group));
        }
        return $this->list[$group];
    }

    /**
     * Checks if the given group exists in this RequireHandler
     * @param string $group The group-name to look for
     * @return bool Returns true if the group exists
     */
    public function hasGroup(string $group): bool{
        return in_array($group, $this->groups);
    }

    /**
     * Required all files from the given group
     * @param string $group group-name from which to require all files
     * @param array $vars
     * @throws \Exception If the requested group does not exist.
     * @return array list of require return values
     */
    public function dispatch(string $group, array $vars = []): array{
        $returns = [];
        $group = $this->getGroup($group);
        extract($vars);
        foreach ($group as $file) {
            $returns[$file] = require_once($file);
        }
        return $returns;
    }
}