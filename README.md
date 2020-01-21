# Laravel Funnel

Filtering results based on the query strings/params passed through the URL is one of the common tasks of everyday web development.

Laravel Funnel is an attempt to reduce this cognitive burden of stacking and applying the filters.

## Installation

Use the package manager [composer](https://getcomposer.org/) to install laravel-funnel.

```bash
composer require tanmaymishu/laravel-funnel
```

## Usage
### Quick Start:
Let's say you have a `Post` model and a boolean attribute `published` and you want to filter all the posts that are published. The URL representation might look like this:

`http://example.com/posts?published=1`

Step 1: Run `php artisan funnel:filter Published`. A new `Published` class inside `app/Filters` directory will be created and the following configurations will be assumed:

- You have an attribute named `published`
- Your have a query string identifier named `published`
- Your desired query clause is `WHERE`
- The operator for the `WHERE` clause is `=`

Don't worry, all these "assumed defaults" can be overridden. Also, you are not limited to boolean fields. You can use any key-value pair of any type as long as they are supported by the operator (See CLI options).

Step 2: Open the model you wish to use this filter in. Add these two statements in your class:

1. `use HasFilters;`
2. `protected $filters = [];`

Then add the filter class in the `$filters` array. Example:

```php
<?php
   
   namespace App;
   
   use Illuminate\Database\Eloquent\Model;
   use TanmayMishu\LaravelFunnel\HasFilters;
   
   class Post extends Model
   {
       use HasFilters;
   
       protected $filters = [
           \App\Filters\Published::class,
       ];
   }
```
Step 3: From the controller, you may call `$posts = Post::filtered();` to get the filtered posts.
### CLI Options
 - This package ships with a `funnel:filter` command. The following command will display all the details including the argument and option it accepts:
    ```php
    php artisan -h funnel:filter
    ```
    ```Description:
    Create a new filter
    
    Usage:
    funnel:filter [options] [--] <name>
    
    Arguments:
    name                         The name of the filter class.
    
    Options:
    -a, --attribute[=ATTRIBUTE]  The attribute name of the model (e.g. is_active). Default: Snake cased filter_class
    -p, --parameter[=PARAMETER]  The name of the request query parameter (e.g. active). Default: Snake cased filter_class
    -o, --operator[=OPERATOR]    The operator for the WHERE clause (e.g. >, like, =, <). Default: =
    -c, --clause[=CLAUSE]        The clause for the query (e.g. where, orderBy, groupBy). Default: where
    ```
- The `filter:funnel` command takes 1 _argument_ (the name of the filter class) and 4 _options_:
    1) Attribute (-a) of the model. If this option is not passed, the attribute will be the snake_cased version of the filter class name that was passed as the argument.
    2) Parameter (-p) or the query string name. Similar to the attribute, it will also use the snake_cased class name as default value, if no `-p` option is provided. 
    3) The operator (-o) for the where clause. The default is the basic `=` operator.
    4) The clause (-c), in case `where` doesn't suit your need.
- Now continuing with the previous example, in addition to filtering published posts, you might also want to sort the posts based on the `title` of the posts.
- Run `php artisan funnel:filter Sort -c orderBy -a title` to create a new filter. It has two options, the `-c` option takes the clause you wish to use instead of `where`. In the case of sorting, it's most likely `orderBy`. If no `-c` is passed, `where` is the default. You should also specify on which attribute of your model you would like the sort to be applied, using the `-a` option. 
- Next, you just have to add the Sort filter to the `$filters` array of the `Post` model:
    ```php
    protected $filters = [
       \App\Filters\Published::class,
       \App\Filters\Sort::class,
    ];
    ```
  The filters are stacked automatically. So the current query strings will take the shape of this:
  `http://example.com/posts?published=1?&sort=desc`
- You can also add a search filter by running `php artisan funnel:filter Search -a title -p q -o LIKE`. This command has 3 options, `-a`, `-p` and `-o`. We passed the attribute (`-a`) name as `title` because we want to search the title of the posts and parameter(`-p`) as `q` so that the default behavior is overridden. This would allow us to use `q` as our query string key. Since a mere `=` operator is not going to be enough for searching, we also provided the operator `LIKE` as the argument of `-o` option.
- Again add the new filter to the `$filters` array:
     ```php
     protected $filters = [
        \App\Filters\Published::class,
        \App\Filters\Sort::class,
        \App\Filters\Search::class,
     ];
     ```
 - The final shape of the url: `http://example.com/posts?published=1&sort=desc&q=foobar`
 
 ### Customization
 - If the generated `apply()` method of the filter class doesn't cover your need, you can always implement your own `apply()` method but it should match the signature of the parent class.
 


## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
