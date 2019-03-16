# WP Valkyrie
This Repository contains the Core-System of Valkyrie, an abstraction layer for the WordPress
ecosystem. It uses a small, extensible API and allows easy development of complex system by
layering simple, standalone, intercommunicating modules.

**It is highly recommended to use a full fledged IDE while working with the Valkyrie system.** 

## System-Core Features
- Split complex Systems into multiple small modules
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
// Loads the Valkyrie-Core and all its modules
require 'valkyrie/Bootstrap.php';
```

## Usage
WP Valkyrie is designed to be used from both the theme and multiple plugins at the same time.
Therefor all Module registration should happen **on** the `after_setup_theme` hook.

### Add a module
Modules are the most important thing when using the Valkyrie-System. Modules provide a behaviour and can be layered
to combine multiple small parts into complex Systems.
Adding a module is pretty straightforward and requires a fixed set of methods to be implemented (\Core\Modules is abstract).
The individual methods will be explained in the following examples. 
```php
// Minimal Module registration
use Core\System;
use Core\Module;
use Core\RequireHandler;

add_action('after_setup_theme',function() {
    System::addModule(new class('%MODULE_NAME%', 10, ['_CORE_']) extends Module{
        
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
use Core\System;
use Core\Admin\Notice;

public function init(): void{
    $this->requireGroup('types');
    $this->requireGroup('meta-boxes');
    
    // Add an admin-notice to indicate successful loading
    System::addNotice(new Notice($this->getName().' loaded', Notice::SUCCESS));
}
```


### Check Plugins
A module can have multiple dependency Plugins. The version parameter (3rd) is optional and allows
defining the minimal version requirements. If the Plugins are not installed or activated. An admin notice will inform you.
```php
use Core\Admin\PluginChecker;

$dependencies = new PluginChecker();
$dependencies->addPlugin('Advanced Custom Fields','advanced-custom-fields/acf.php'); // ACF any version
$dependencies->addPlugin('Contact Form 7', 'contact-form-7/wp-contact-form-7.php','5.0.1'); // CF7 version 5.0.1 or higher
$dependencies->checkPlugins();
```

### Admin Notice
This has already been introduced in the `require()` method above.
```php
use Core\System;
use Core\Admin\Notice;

// no color (default)
System::addNotice(new Notice('plain', Notice::NONE));
System::addNotice(new Notice('plain'));

// success
System::addNotice(new Notice('success', Notice::SUCCESS));

// warning
System::addNotice(new Notice('warning', Notice::WARNING));

// error
System::addNotice(new Notice('error', Notice::ERROR));

// notice is dismissible
System::addNotice(new Notice('warning can be dismissed', Notice::WARNING, true));
```

### Forms
The Valkyrie-Core comes with a basic Form-Builder which is meant for usage within the Admin-Panel
in meta-boxes, admin-pages and widgets.
```php
use Core\Form;

$form = new Form('form-id');

$form->addElement(new Form\Element\Editor('editor')); // WP-Editor
$form->addElement(new Form\Element\Input('name', 'E-Mail', 'email')); // Input type email
$form->addElement(new Form\Element\Button('Save Changes', ['button-primary'])); // submit button

// Handles saving and rendering the form.
// Depending on use-case this is not required every time
$form->dispatch();
```

### Option Page
Adding options-pages to the WP-Backend is really simple. **Option pages support the Core form-builder.**
Data gets saved to the global options.
```php
use Core\System;
use Core\Admin\Page;
use Core\Form;

// Page is created
$page = new Page('Page Title', function(): void{
    echo '<h1>Hello World</h1>';
    
    // Form Support
    $form = new Form('form-id');    
    $form->addElement(new Form\Element\Editor('editor'));
    $form->addElement(new Form\Element\Button('Save Changes', ['button-primary']));
    $form->dispatch();
    
}, 'read', 'dashicons-star-filled', 1);

// A Suppage is added to the page above
$page->addChildPage(new Page('Subpage Title', function(): void{
    echo '<h1>This is the Subpage</h1>';
}));

// The Page-Tree is added to the core
System::addPage($page);
```

### Meta Boxes
Adding meta-boxes to the WP-Backend is really simple as well. It is meant to be used with the
Core form-builder and saves data to the current posts post_meta.
```php
use Core\System;
use Core\Admin\Meta;
use Core\Form;

// Metaboxes are meant to be used with the formbuilder
$form = new Form('form-id');
$form->addElement(new Form\Element\Input('name', 'Label'));

// Metaboxes can be added to multiple post types or WP_Screens
$meta = new Meta('meta-id','Title', $form, ['post']);
System::addMeta($meta);
```

### Widgets
Building a custom widget is really simple. You only have to define its name and provide two
functions. One for rendering and one which builds the form using the Core form-builder.
```php
use Core\System;
use Core\Admin\Widget;
use Core\Form;

$widget = new Widget('widget-id','Widget Name', 'Description', function(array $values, array $atts): void{
    echo $values['headline'];
}, function(Form $form): void{
    $form->addElement(new Form\Element\Input('headline', 'Label'));
});

System::addWidget($widget);
```

### Cross Module Communication
To enable information flow between Core-Modules, the System provides an optional interface, which allows
your module to provide a public API to the outer world or other modules.

To allow your `Module` to have a public API, you need to implement the `API` interface. This will force
you to implement the `getPipeline()` method, where you can provide a pipeline object, which handles all
API-Requests.

```php
use Core\System;
use Core\Module;
use Core\RequireHandler;
use Core\API;
use Core\Pipeline;

add_action('after_setup_theme',function() {
    System::addModule(new class('foobar', 10, ['_CORE_']) extends Module implements API{
        /**
         * Returns the current Modules API-Pipeline instance
         * @return Pipeline
         */
        public function getPipeline(): Pipeline{
            // Our pipeline instance
            $pipeline = new class() extends Pipeline{
                // methods can be defined as usual
                public function hello(string $object): string{
                    return 'Hello ' . $object;
                }
            };
            return $pipeline;
        }
    });
});
```

After you have implemented all your public methods, you can access these methods using a `System::API`
call. Direct access is possible, but prone to fatal errors. Using `access` is preferred, because
an `Exception` is thrown, if the requested method does not exist.
```php
$fooAPI = System::API('foobar');
echo $fooAPI->hello('world'); // possible
echo $fooAPI->access('hello', 'world'); // preferred
```

### Module dependencies
New Modules can have dependencies of other modules. The Module will only load if all dependencies can be resolved.
`_CORE_` represents all main Valkyrie-Modules and as long as you are using any of its functions, it is highly recommended
to be dependent on it.
```php
new class('%MODULE_NAME%', 10, ['_CORE_', 'foo', 'bar']) extends Module{
    // ...
}
``` 
If you are using external Module APIs within your Module using `System::API($moduleName)`
don't forget to include the accessed Module as a dependency to prevent any Errors.