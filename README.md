# Laravel Funnel

Filtering results based on the query strings/params passed through the URL is one of the common tasks of everyday web development.

Laravel Funnel is an attempt to reduce the cognitive burden of stacking and applying the filters.

## Features
- [x] **Param-Attr binding:** Binds query string _parameters_ to eloquent model _attribute_.
- [x] **Code generation:** Generates filter classes with a simple command.
- [x] **Multi-value params:** Makes multi-value parameters painless by allowing comma-delimited list in URL. Example: `http://example.com/posts?title=foo,bar`.
- [x] **Sorting:** Makes your filter "sort-aware" by providing a simple `--clause=orderBy` argument.
- [x] **Searching:** Makes your filter "search-aware" by providing a simple `--operator=like` argument.
- [x] **Related model's attr binding:** Binds attribute from a relation easily: `--attribute=comments.replies.body`
- [x] **Customization:** Logic in filter classes can be overridden according to your need.
 
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
Step 3: From the controller, you may call `$posts = Post::filtered();` to get the filtered posts. It returns an instance of Builder, allowing you to further chain the query:
`Post::filtered()->with('comments')->get()`. You have to append `->get()` as you would normally do, to return the result as a collection.
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
- The `funnel:filter` command takes 1 _argument_ (the name of the filter class) and 4 _options_:
    1) Attribute (`--attribute` or, `-a`) of the model. If this option is not passed, the attribute will be the snake_cased version of the filter class name that was passed as the argument.
    2) Parameter (`--parameter` or, `-p`) or the query string parameter's name. Similar to the attribute, it will also use the snake_cased class name as default value, if no `-p` option is provided. 
    3) The operator (`--operator` or, `-o`) for the where clause. The default is the basic `=` operator.
    4) The clause (`--clause` or, `-c`), in case `where` doesn't suit your need.
- Now continuing with the previous example, in addition to filtering published posts, you might also want to sort the posts based on the `title` of the posts.
- Run `php artisan funnel:filter Sort --clause=orderBy --attribute=title` to create a new filter. It has two options, the `-c` option takes the clause you wish to use instead of `where`. In the case of sorting, it's most likely `orderBy`. If no `-c` is passed, `where` is the default. You should also specify on which attribute of your model you would like the sort to be applied, using the `-a` option. 
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
 ### Multi-value GET parameters (`[]` notation)
 - Funnel can understand multi-value GET parameters:
 `http://example.com/posts?title[]=foo&title[]=bar`. You don't have to take any extra steps for that.
 - As you can see, you will need to append the array notation `[]` to your query parameter's name.
 - Funnel will pass the parameter values (foo & bar) through the `OR` sub-queries.
 - A get request like `http://example.com/posts?title[]=foo&title[]=bar` will indicate that we want to fetch all the posts that has a title _foo_ or _bar_. 
 ### Multi-value GET parameters (`,` notation)
 - In addition to the `[]` notation, Funnel provides an easier, alternative comma (`,`) syntax for multi-value parameters: `http://example.com/posts?title=foo,bar`
 - The advantage is that you don't have to keep repeating `param[]` for each value.
 ### Binding related model's attribute
 - The attribute doesn't necessarily have to reside in the model being queried. If you're in a situation where you want to filter all the posts with the comment body "Foo", assuming your Post model has a `comments()` relationship, you should pass the `--attribute=comments.body` option:
    - Example Command: `php artisan funnel:filter Comment --attribute=comments.body`. Funnel will filter all the posts with the specified comment body even though the `body` attribute lives in the `Comment` model and not in the `Post` model.
    - Example URL: `http://example.com/posts?comment=Foo` 
 - Even nested related model's attribute can be bound. If we want to fetch all the posts that have the reply body `Bar` in the comments, we can achieve that.
    - Example Command: `php artisan funnel:filter Reply --attribute=comments.replies.body`
    - Example URL: `http://example.com/posts?reply=Bar` 

 ### Customization
 - If the generated `apply()` method of the filter class doesn't cover your need, you can always implement your own `apply()` method but it should match the signature of the parent class.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
