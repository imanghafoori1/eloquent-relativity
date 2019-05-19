# Eloquent Relativity

This allows you to decouple your eloquent models from one another.

[![Build Status](https://travis-ci.org/imanghafoori1/eloquent-relativity.svg?branch=master)](https://travis-ci.org/imanghafoori1/eloquent-relativity)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/imanghafoori1/eloquent-relativity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/eloquent-relativity/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/imanghafoori1/eloquent-relativity/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/imanghafoori1/eloquent-relativity/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/imanghafoori/eloquent-relativity/v/stable)](https://packagist.org/packages/imanghafoori/eloquent-relativity)

### A problem which stops true modularity :

Let's face it, imagine you have a modular blog application.

Then, you want to add a `commenting` feature to it, so that users can comment on your articles.

In a modular structure, you have 2 modules (`user` module and `blog` module) and you will add a new module for `comments`

### let's analyze dependencies and couplings :

Here the `blog` module "knows" and "depends" upon the `user` module.

But the `user` module should not know or care about the `blog` module. The `blog` is a `plug-in` on the top of the `user` module.

Now we want to add a `comment` module, on the top of `user` and `blog` module.

![Diagram](https://user-images.githubusercontent.com/6961695/57987611-26242a00-7a99-11e9-8c32-67e76b57420f.jpg)

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
 
 ![Diagram](https://user-images.githubusercontent.com/6961695/57987611-26242a00-7a99-11e9-8c32-67e76b57420f.jpg)
 

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
