<?php

namespace Core\Admin;

/**
 * Handles Pages for the WP-Admin-Panel
 * @package Core\Wordpress
 */
class Page{

    /**
     * List of AdminPages, which are children of the current AdminPage
     * @var Page[]
     */
    private $childPages;

    /**
     * The Dashicon String for the current AdminPage
     * only applies if Page is not a child-page
     * @var string
     */
    private $icon;

    /**
     * The PagesPosition in the admin panel
     * only applies if Page is not a child-page
     * @var int
     */
    private $position;

    /**
     * The Adminpages title
     * @var string
     */
    private $title;

    /**
     * The capability required for this menu to be displayed to the user.
     * @var string
     */
    private $capability;

    /**
     * The function to be called to output the content for this page.
     * @var \Closure
     */
    private $handler;

    /**
     * AdminPage constructor.
     * @param string $title The text to be used for the menu.
     * @param \Closure $handler  The function to be called to output the content for this page.
     * @param string $capability The capability required for this menu to be displayed to the user.
     * @param string $icon Dashicon String
     * @param int $position Position in the Admin-Panel
     */
    public function __construct(string $title, \Closure $handler, string $capability = 'read', string $icon = 'dashicons-heart', int $position = 1000){
        $this->title = $title;
        $this->capability = $capability;
        $this->childPages = [];
        $this->icon = $icon;
        $this->position = $position;
        $this->handler = $handler;
    }

    /**
     * Adds a child-page to the current AdminPage
     * @param Page $page the new child-page
     */
    public function addChildPage(Page $page): void{
        array_push($this->childPages, $page);
    }

    /**
     * Attaches the current AdminPage to the WP-Backend
     * @param Page|null $page the parent-page this page gets attached to
     */
    public function dispatch(Page $page = null): void{
        if (is_null($page)){
            add_menu_page($this->title, $this->title, $this->capability, $this->getSlug(), $this->handler, $this->icon, $this->position);
        }
        else{
            add_submenu_page($page->getSlug(), $this->title, $this->title, $this->capability, $this->getSlug(), $this->handler);
        }
        foreach ($this->childPages as $childPage){
            $childPage->dispatch($this);
        }
    }

    /**
     * Returns the AdminPages title as a sanitized slug
     * @return string The sanitized title
     */
    public function getSlug(): string{
        return sanitize_title($this->title);
    }
}