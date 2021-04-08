# Laravel Funnel

Filtering results based on the http query string parameter (`?key=value`) is one of the common tasks of everyday web development.

Laravel Funnel is an attempt to reduce the cognitive burden of applying and maintaining the filters.

## Features
- [x] **Param-Attr binding:** Binds query string _parameters_ to eloquent model _attributes_.
- [x] **Code generation:** Generates filter classes with a simple command.
- [x] **Multi-value params:** Makes multi-value parameters painless by allowing comma-delimited list in URL. Example: `http://example.com/posts?title=foo,bar`.
- [x] **Sorting:** Creates "sort-aware" filters with a simple `--clause=orderBy` argument.
- [x] **Searching:** Creates "search-aware" with a simple `--operator=like` argument.
- [x] **Related model's attr binding:** Binds attribute from a related model easily with `relation.attribute` format:  `--attribute=comments.body`
- [x] **Eager-loading:** Funnel comes with eager-loading support out of the box. Pass your relation to the default `?with` query param. Example: `http://example.com/posts?with=comments,categories`.
- [x] **Customization:** Query logic in generated filter classes can be overridden according to your need.
 
## Installation

Use the package manager [composer](https://getcomposer.org/) to install laravel-funnel.

```bash
composer require tanmaymishu/laravel-funnel
```

## Usage
### Quick Start:
Let's say you have a `Post` model and an attribute `published` and you want to filter all the posts that are published. The URL representation might look like this:

`http://example.com/posts?published=1`

Step 1: Run `php artisan funnel:filter Published`. A new `Published` class inside `app/Filters` directory will be created and the following configurations will be assumed:

- You have an attribute named `published`
- Your have a query string identifier named `published`
- Your desired query clause is `WHERE`
- The operator for the `WHERE` clause is `=`

Don't worry, all these "assumed defaults" can be overridden (See [CLI options](https://github.com/tanmaymishu/laravel-funnel#cli-options) below).

Step 2: Open the model (e.g. Post.php) where you want to use this filter in. Add these two lines in your class:

```php
use HasFilters;
protected $filters = [];
```

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
Step 3: Now you can call `Post::filtered()` to get the filtered posts. It returns an instance of Builder, allowing you to further chain the query, for example:
`Post::filtered()->with('comments')->get()`. You have to append `->get()` as you normally would, to return the result as a collection.

You can add as many filters as you want in the `$filters` array. Append the parameter in your query string: `?title=foo&published=1` and Funnel will pick up the appropriate filter for you.
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
- The `funnel:filter` command takes 1 _argument_ (the name of the filter class) and 4 _options_ (sometimes known as _flags_):
    1) `--attribute=` (short form: `-a`): The attribute of the model. If this option is not provided, the default _attribute_ will be the snake_cased form of the filter class' name that was provided as the _argument_.
    2) `--parameter=` (short form: `-p`): The query string parameter that will be received from the URL. If this option is not provided, the default _parameter_ will be the snake_cased form of the filter class' name that was provided as the _argument_. 
    3) `--operator=` (short form: `-o`): The operator to be used in the `WHERE` clause. If this option is not provided, `=` will be used as the default operator.
    4) `--clause=` (short form: `-c`): The clause to be used in the query. If `WHERE` clause doesn't suit your need, you can specify a different clause (currently supported: `orderBy`, `groupBy`)
    
Notes:
- If the operator is `like`, the parameter's value will be surrounded by the `%` wildcard on both sides of the value. This behaviour may be customized in future.
- If the clause is `orderBy`, only one of the following two parameter values are expected: a) **asc** b) **desc** 
    
### Examples
 Let's take a look at some funnel commands and what result they produce based on the URL:
 
 **Model and Relation Considerations:**
 ```php
// A Post hasMany Comments and a Comment hasMany Replies
// Post is the model that we want to query.

Post::createMany([
    ['title' => 'Foo', 'body' => 'Lorem ipsum'], // We'll call it Post 1
    ['title' => 'Bar', 'body' => 'Dolor sit amet'], // We'll call it Post 2
]);
Comment::createMany([
    ['body' => 'Comment A', 'post_id' => 1],
    ['body' => 'Comment B', 'post_id' => 2],
]);
Reply::createMany([
    ['content' => 'Reply A', 'comment_id' => 1],
    ['content' => 'Reply B', 'comment_id' => 2],
]);
```

**[Note: The examples below use a mixture of long form options and short form options. Feel free to use any form you like.]**
 
 | **Command**   |      **URL**      |  **Result** |
 |:--------------|:------------------|-------------|
 | `funnel:filter Title` |  example.com?title=Foo | Fetches Post 1 |
 | `funnel:filter Title` |  example.com?title=Foo,Bar | Fetches Post 1 and 2 |
 | `funnel:filter Title` |  example.com?title[]=Foo&title[]=Bar | Fetches Post 1 and 2 |
 | `funnel:filter Title --clause=orderBy` |    example.com?title=asc   |   Fetches all the posts sorted by title in ascending order |
 | `funnel:filter Title -c orderBy` (Shorthand) | example.com?title=desc |    Fetches all the posts sorted by title in descending order |
 | `funnel:filter Body --operator=like` | example.com?body=Lorem |    Fetches Post 1 |
 | `funnel:filter Search -a body -o like` | example.com?search=Dolor |    Fetches Post 2. Specified attr (body) and operator (like) is used instead of defaults. |
 | `funnel:filter Comment -a comments.body -o like` | example.com?comment=Comment B |    Fetches Post 2. Will return all the posts that contain "Comment B" in their comment's body. `body` attr of Comment model is used instead of the `body` attr of the Post model.  |
 | `funnel:filter Reply -a comments.replies.content -o like` | example.com?reply=Reply A |    Fetches Post 1. Will return all the posts that contain "Reply A" in their replies to a comment. `content` attr of Reply model is used.  |
 
 ### Multi-value parameters (`[]` notation)
 - Funnel can understand multi-value query string parameters:
 `http://example.com/posts?title[]=foo&title[]=bar`. You don't have to take any extra steps for that.
 - As you can see, you will need to append the array notation `[]` to each of your query parameters.
 - Funnel will pass the parameter values (foo & bar) through the `OR` sub-queries.
 - A get request like `http://example.com/posts?title[]=foo&title[]=bar` will indicate that we want to fetch all the posts that has a title _foo_ or _bar_. 
 ### Multi-value parameters (`,` notation)
 - In addition to the `[]` notation, Funnel provides an easier, alternative comma (`,`) syntax for multi-value parameters: `http://example.com/posts?title=foo,bar`
 - The advantage of `,` over `[]` notation is that you don't have to keep repeating `param[]` for each parameter.
 ### Binding related model's attribute
 - The attribute doesn't necessarily have to reside in the model being queried. If you're in a situation where you want to filter all the posts with the comment body "Foo", assuming your Post model has a `comments()` relation, you should pass the `--attribute=comments.body` option:
    - Example Command: `php artisan funnel:filter Comment --attribute=comments.body`. Funnel will filter all the posts with the specified comment body even though the `body` attribute lives in the `Comment` model and not in the `Post` model.
    - Example URL: `http://example.com/posts?comment=Foo` 
 - Even nested related model's attribute can be bound to a parameter. If we want to fetch all the posts that have the reply body `Bar` in the comments, we can achieve that too as long as the relationships exist:
    - Example Command: `php artisan funnel:filter Reply --attribute=comments.replies.body`
    - Example URL: `http://example.com/posts?reply=Bar` 

 ### Eager-loading
 - Funnel comes with eager-loading support out of the box. Pass your relation to the default `?with` query param. Example: `http://example.com/posts?with=comments,categories`.
 - If you need to customize the eager key name, which is by default `with`, you can do so from `config/funnel.php`. Before you do so, you need to publish your config files by running the following command:
`php artisan vendor:publish --provider="TanmayMishu\LaravelFunnel\FunnelServiceProvider"`

 ### Customization
 - If the generated `apply()` method of the filter class doesn't fit your need, you can always implement your own `apply()` method but it should match the signature of the parent class.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

## License
[MIT](https://choosealicense.com/licenses/mit/)
