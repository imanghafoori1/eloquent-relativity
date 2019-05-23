# Eloquent Relativity

This allows you to decouple your eloquent models from one another, by defining relations dynamically at run-time.

   <img width="600px" src="https://user-images.githubusercontent.com/6961695/57988296-be261180-7aa1-11e9-9e28-645ab0da75dd.png" alt="widgetize_header"></img>

[![Build Status](https://travis-ci.org/imanghafoori1/eloquent-relativity.svg?branch=master)](https://travis-ci.org/imanghafoori1/eloquent-relativity)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/imanghafoori1/eloquent-relativity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/eloquent-relativity/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/eloquent-relativity/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/eloquent-relativity/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/imanghafoori/eloquent-relativity/v/stable)](https://packagist.org/packages/imanghafoori/eloquent-relativity)
[![StyleCI](https://github.styleci.io/repos/186496125/shield?branch=master)](https://github.styleci.io/repos/186496125)

### A problem which stops true modularity :

Let's face it, imagine you have a modular blog application.

Then, you want to add a `commenting` feature to it, so that users can comment on your articles.

In a modular structure, you have 2 modules (`user` module and `blog` module) and you will add a new module for `comments`

### let's analyze dependencies and couplings :

Here the `blog` module "knows" and "depends" upon the `user` module.

But the `user` module should not know or care about the `blog` module. The `blog` is a `plug-in` on the top of the `user` module.

Now we want to add a `comment` module, on the top of `user` and `blog` module.


<img width="600px" src="https://user-images.githubusercontent.com/6961695/57987611-26242a00-7a99-11e9-8c32-67e76b57420f.jpg"></img>


### The Right way :

In a truely modular system when you add the `comments`, you should NOT go and touch the code within the `users` or `blog` module.
(Remember the `open-closed` principle in `SOLID` ?!)

Imagine you are in a team and each member is working on a seperate module.

`Blog` module is not yours. your team mate is responsible for it and is allowed to code on it.

But when you want to start to define the eloquent relations between `Comment` and `User` and `Article` models, you immediately realize that you have to put code on the eloquent models of other modules to define the inverse of the relationships. Crap ! 


Look How everything is pointing inward.

If you look at the `User` folder you will have absolutely no footprint of Comment or Article.

We have to touch the code of both `Blog` and `User` module when add a new `comment` module.

For example : You have to open `User.php` and define the
```php
public function comments() {
    return $this->hasMany(Comment::class); 
}
```
and this is a no no, because it makes an arrow from inside to outside.

So what to do ?!

How can `Comment` be introduced to the system without modifying the other modules ?! (@_@)


### Install laravel-relativity : (the most painful step)

```
composer require imanghafoori/eloquent-relativity   (and take a coffee...)
```

Now the installtion finished, you first have to make your models "relative" !!!

By using the `Imanghafoori\Relativity\DynamicRelations` traits on your eloquent models.

![image](https://user-images.githubusercontent.com/6961695/58089939-465c0200-7bdb-11e9-8df0-2dc5212ced43.png)


So the `User`, `Article`, `Comment` will have to have this trait one them.

Now comes the sweet part :

within the `CommentsServiceProvider.php`

```php
class CommentsServiceProvider 
{
    public function register () {
        
        User::has_many('comments', Comment::class);     // instead of defining method on the User class.
        Article::has_many('comments',  Comment::class);
        
        Comment::belongs_to('author', User::class);       // inverse of relations
        Comment::belongs_to('article',  Article::class);
    }

}

```


Now you can do these queries :

```php
User::find(1)->comments;
or 
User::find(1)->comments()->count();
```

So instead of going to `User` model and define a method there...
```php
public function comments() {
    return $this->hasMany(Comment::class); 
}
```

You have defined the method remotely from your new module at run-time: 

 ```php
 User::has_many('comments', Comment::class);
 ```
 
 Here is a list of supported relations :
 
- has_many
- has_one
- belongs_to
- belongs_to_many
- morph_to_many
- morph_many
- morph_one
- morph_to
- morphed_by_many
- has_many_through
 
They accept the same paramters as the eloquent equivalent counter part. except the first argument should be relation name.

### Extra features :


sometimes you need to call extra methods on the relations.

```php
User::has_many('comments', Comment::class)->orderBy('id', 'asc');
```

All the methods are available to you.

- Enforce eager-loading

On reqular eloquent models you may define the

```php
User extends Model {
    protected $with = ['comments'];
}
```

instead you can :

```php

User::forceEagerLoading('comments');

```

remember this should be in the `boot` method of your Service Provider not the `register` method.


### :star: Your Stars Make Us Do More :star:
As always if you found this package useful and you want to encourage us to maintain and work on it, Please press the star button to declare your willing.


# More from the author:

### Laravel Terminator

 :gem: A minimal yet powerful package to give you opportunity to refactor your controllers.

- https://github.com/imanghafoori1/laravel-terminator

------------------

### Laravel Widgetize

 :gem: A minimal yet powerful package to give a better structure and caching opportunity for your laravel apps.

- https://github.com/imanghafoori1/laravel-widgetize


-------------------

### Laravel Master Pass

 :gem: A simple package that lets you easily impersonate your users.

- https://github.com/imanghafoori1/laravel-MasterPass

------------------

### Laravel HeyMan

 :gem: It allows to write exressive and defensive code whcih is decoupled from the rest of your app.

- https://github.com/imanghafoori1/laravel-heyman
