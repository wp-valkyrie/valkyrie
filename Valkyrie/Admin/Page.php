<?php

namespace Valkyrie\Admin;

/**
 * Handles Pages for the WP-Admin-Panel
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
     * The WP Parent this page is attached to
     * @var bool|string
     */
    private $wpParent = false;

    /**
     * True if this page is rendered on the Multisite Admin Page
     * @var bool
     */
    private $isMultisite = false;

    /**
     * AdminPage constructor.
     * @param string $title The text to be used for the menu.
     * @param \Closure $handler The function to be called to output the content for this page.
     * @param string $capability The capability required for this menu to be displayed to the user.
     * @param string $icon Dashicon String
     * @param int $position Position in the Admin-Panel
     */
    public function __construct(string $title, \Closure $handler, string $capability = 'read', string $icon = 'dashicons-admin-generic', int $position = 1000){
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
     * Adds a WP-Internal Parent page using the page slug
     * @param string $parent the parent page slug
     */
    public function pushWpParent(string $parent): void{
        $this->wpParent = $parent;
    }

    /**
     * Sets the multisite parameter, on true
     * this page will get rendered on the network admin page only
     * @param bool $set
     */
    public function setMultisite(bool $set){
        $this->isMultisite = $set;
    }

    /**
     * Renders the Page-Content with the given handler function
     */
    public function pageHandler(): void{
        echo '<div class="wrap">';
        ($this->handler)();
        echo '</div>';
    }

    /**
     * Attaches the current AdminPage to the WP-Backend
     * @param Page|null $page the parent-page this page gets attached to
     */
    public function dispatch(Page $page = null): void{
        if (is_null($page)) {
            if ($this->wpParent) {
                add_submenu_page($this->wpParent, $this->title, $this->title, $this->capability, $this->getSlug(), [$this, 'pageHandler']);
            } else {
                add_menu_page($this->title, $this->title, $this->capability, $this->getSlug(), [$this, 'pageHandler'], $this->icon, $this->position);
            }
        } else {
            add_submenu_page($page->getSlug(), $this->title, $this->title, $this->capability, $this->getSlug(), [$this, 'pageHandler']);
        }
        foreach ($this->childPages as $childPage) {
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

    /**
     * Returns true if this page is registered on the network admin panel
     * @return bool
     */
    public function isMultisite(): bool{
        return $this->isMultisite;
    }
}