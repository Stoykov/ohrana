## Ohrana - Role-Based Access Control List for Lumen framework

### TO DOs

 * Make better documentation
 * Add method listing in rules (App\Http\Controllers\ExampleController@example1;example2;example3)
 * Add exclusion rules (e.g. you can access everything, except my bank account)
 * Add policy based rules (e.g. if user has 20 posts, he can start a new thread)

### How does it work

Ohrana is a granular role-and-rule-based access control list. What does 'granular' mean? It means that you can give a role permissions for a specific method in a specific controller in a specific namespace, or you can give a role access to a whole namespace, or you can give a role global access, all that without changing a single line in your code.

Permissions are rule-based and are bound to roles, which means that every role has it's own set of permissions, unlike traditional ACL libraries where you have general permissions such as 'Edit Post' which can be attached to multiple roles. Rules are simple strings with delimiters that describe the access that that permission grants.

`App\Http\Controllers\ExampleController@example` grants access to the example method of ExampleController.
`App\Http\Controllers\ExampleController@example;test;foo` grants access to the example, test, foo methods of ExampleController.
`App\Http\Controllers\ExampleController@All` grants access to all methods in ExampleController.
`App\Http\Controllers\` grants access to all controllers in the App\Http\Controllers\ namespace.
`All` grants global access.

##### Pros?
This model of ACL is very flexible and granular. You can say that **Junior Staff** members can access `BlogController@view` and `BlogController@edit`, but not `BlogController@delete` until they have 20 days of service.

##### Cons?
As already mentioned this model is very granular and requires a lot of managing if you want to use it's full capabilities.

### Installation

 * Install via composer `composer require stoykov/ohrana`
 * Register `stoykov\Ohrana\OhranaServiceProvider.php` in your `bootstrap/app.php` file
 * Optionally you can add an alias to the Ohrana facade in `bootstrap/app.php`

 ```php
 class_alias('stoykov\Ohrana\Facades\Ohrana', 'Ohrana');
 ```

### How to use

##### Adding middleware
In order to protect a route you need to register the `OhranaMiddleware` in your app and add it to your routes.

##### Adding traits to user model
`stoykov\Ohrana\Traits\OhranaRole` trait needs to be added to your user model. This adds the `hasPermission` method which checks whether this user has access to the resource requested.

##### Repositories
You can have your own Role and Permission models. All you need to do is write your own repositories implementing `stoykov\Ohrana\Repositories\Role` and `stoykov\Ohrana\Repositories\Permission` interfaces respectfully and change the two namespaces in the configuration file.

##### Scaning paths
Ohrana scans paths for controllers, when it finds a controller it gets all it's methods and caches them. By default only the `app/Http/Controllers/*` path is scanned for controllers, but you can add more paths in the configuration file. To list all available Namespaces/Controllers/Methods call the `Ohrana::all()` method from the Ohrana facade. Or you can always write your rules by hand.

##### Adding roles

##### Attaching permissions