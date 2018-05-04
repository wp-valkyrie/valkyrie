# WP-Core
This Repository contains a Core-System for WordPress themes, plugins and anything else.
It uses a small, extensible API and allows easy development of complex modules.

**It is highly recommended to use a full fledged IDE while working with the WP-Core.** 

## Core Features
- Check plugin dependencies for individual modules
- Enqueue assets with ease
- Load module files without hassle
- Create admin-pages, admin-notices, meta-boxes and widgets with a straightforward API

## Setup
You can add the Core as a normal wp-plugin or add it as a fixed dependency to your theme.
### Load Core as Plugin
Clone this repository into your plugin folder
`git clone https://github.com/jschaefer-io/wp-core.git` and activate it through the admin panel.
The core and all its modules will get loaded automatically.

### Load Core in Theme
Clone this repository into your theme-folder `git clone https://github.com/jschaefer-io/wp-core.git`
and add the following line to your `functions.php`.
```php
// Loads the WP-Core and all its modules
require 'wp-core/Bootstrap.php';
```

## Usage
WP-Core is designed to be used from both the theme and multiple plugins at the same time.
Therefor all Module registration should happen **on** the `after_setup_theme` hook.

### Add a module
Adding module is pretty straightforward and required a fixed set of methods to be implemented (\Core\Modules is abstract).
The individual methods will be explained in the following examples. 
```php
// Minimal Module registration
use Core\Module;
use Core\RequireHandler;

add_action('after_setup_theme',function() {
    Core::addModule(new class('%MODULE_NAME%', 10) extends Module{
        
        // Gets executed when all modules are registered
        public function init(): void{}

        // Is used to defined required files for this module
        public function require(RequireHandler $handler): RequireHandler{
            return $handler;
        }

        // Enqueues assets in the frontend
        public function enqueue(): void{}

        // Enqueues assets in the backend
        public function adminEnqueue(): void{}
    });
});
```

#### The `enqueue()` method
This method gets called to enqueue scripts or styles to the frontend.
```php
// Example usage: Loads the themes main js and css files
public function enqueue(): void{
    wp_enqueue_script( 'app-js', get_stylesheet_directory_uri() . '/assets/app.js', ['jquery'], '1.0', true );
    wp_enqueue_style( 'app-css', get_stylesheet_directory_uri() . '/assets/app.css', [], '1.0', 'all');
}
```

#### The `adminEnqueue()` method
This method gets called to enqueue scripts or styles to the backend.
```php
// Example usage: Includes the WP-Media script if it has not been enqueued
public function adminEnqueue(): void{
    if ( ! did_action( 'wp_enqueue_media' ) ) {
        wp_enqueue_media();
    }
}
```

#### The `require()` method
This method lets you prepare groups of includes, which can be loaded in the `init()` method.
```php
// Example usage: Stage a some files used by this module
public function require(RequireHandler $handler): RequireHandler{
    $handler->addFile(__DIR__ . '/types/*.php', 'types'); // Stages all PHP-files in the types folder
    
    // Stages the two files into one Group
    $handler->addFile(__DIR__ . '/meta/myMeta1.php', 'meta-boxes');
    $handler->addFile(__DIR__ . '/meta/myMeta2.php', 'meta-boxes');
    return $handler;
}
```

#### The `init()` method
This method is the main entry point for your module. From here you can access the Core-API
and load any stages files from the `require()` method.
```php
// Example usage: Loads the required groups and show success
use Core\Admin\Notice; // needs to be at top of file

public function init(): void{
    $this->requireGroup('types');
    $this->requireGroup('meta-boxes');
    
    // Add an admin-notice to indicate successful loading
    Core::addNotice(new Notice($this->getName().' loaded', Notice::SUCCESS));
}
```


### Check Plugins
A module can have multiple dependency Plugins. The version parameter (3rd) is optional and allows
defining the minimal version requirements. If the Plugins are not installed or activated. An admin notice will inform you.
```php
use Core\Admin\PluginChecker; // needs to be at top of file

$dependencies = new PluginChecker();
$dependencies->addPlugin('Advanced Custom Fields','advanced-custom-fields/acf.php'); // ACF any version
$dependencies->addPlugin('Contact Form 7', 'contact-form-7/wp-contact-form-7.php','5.0.1'); // CF7 version 5.0.1 or higher
$dependencies->checkPlugins();
```

### Admin Notice
This has already been introduced in the `require()` method above.
```php
use Core\Admin\Notice; // needs to be at top of file

// no color (default)
Core::addNotice(new Notice('plain', Notice::NONE));
Core::addNotice(new Notice('plain'));

// success
Core::addNotice(new Notice('success', Notice::SUCCESS));

// warning
Core::addNotice(new Notice('warning', Notice::WARNING));

// error
Core::addNotice(new Notice('error', Notice::ERROR));

// notice is dismissible
Core::addNotice(new Notice('warning can be dismissed', Notice::WARNING, true));
```

### Forms
The WP-Core comes with a basic Form-Builder which is meant for usage within the Admin-Panel
in meta-boxes, admin-pages and widgets.
```php
use Core\Admin\Form; // needs to be at top of file

$form = new Form('form-id');

$form->addElement(new Form\Editor('editor')); // WP-Editor
$form->addElement(new Form\Input('name', 'E-Mail', 'email')); // Input type email
$form->addElement(new Form\Button('Save Changes', ['button-primary'])); // submit button

// Handles saving and rendering the form.
// Depending on use-case this is not required every time
$form->dispatch();
```

### Option Page
Adding options-pages to the WP-Backend is really simple. **Option pages support the Core form-builder.**
Data gets saved to the global options.
```php
use Core\Admin\Page; // needs to be at top of file
use use Core\Admin\Form; // needs to be at top of file

// Page is created
$page = new Page('Page Title', function(): void{
    echo '<h1>Hello World</h1>';
    
    // Form Support
    $form = new Form('form-id');    
    $form->addElement(new Form\Editor('editor'));
    $form->addElement(new Form\Button('Save Changes', ['button-primary']));
    $form->dispatch();
    
}, 'read', 'dashicons-star-filled', 1);

// A Suppage is added to the page above
$page->addChildPage(new Page('Subpage Title', function(): void{
    echo '<h1>This is the Subpage</h1>';
}));

// The Page-Tree is added to the core
Core::addPage($page);
```

### Meta Boxes
Adding meta-boxes to the WP-Backend is really simple as well. It is meant to be used with the
Core form-builder and saves data to the current posts post_meta.
```php
use Core\Admin\Meta; // needs to be at top of file
use use Core\Admin\Form; // needs to be at top of file

// Metaboxes are meant to be used with the formbuilder
$form = new Form('form-id');
$form->addElement(new Form\Input('name', 'Label'));

// Metaboxes can be added to multiple post types or WP_Screens
$meta = new Meta('meta-id','Title', $form, ['post']);
Core::addMeta($meta);
```

### Widgets
Building a custom widget is really simple. You only have to define its name and provide two
functions. One for rendering and one which builds the form using the Core form-builder.
```php
use Core\Admin\Widget; // needs to be at top of file
use use Core\Admin\Form; // needs to be at top of file

$widget = new Widget('widget-id','Widget Name', 'Description', function(array $values, array $atts): void{
    echo $values['headline'];
}, function(Form $form): void{
    $form->addElement(new Form\Input('headline', 'Label'));
});

Core::addWidget($widget);
```