# Eloquent Relativity

This allows you to decouple your eloquent models from one another.

### A problem which stops true modularity :

Let's face it, imagine you have a blog application.

Then, you want to add a `commenting` feature to it, so that users can comment on your articles.

In a modular structure, you have 2 modules (`user` module and `blog` module) and you will add the a new module for `comments`

### let's analyze dependencies and couplings :

Here the `blog` module "knows" and "depends" upon the `user` module.

but the `user` module should not know or care about the `blog` module. The `blog` is a `plug-in` on the top of the `user` module.

Now we want to add a `comment` module, on the top of `user` and `blog` module.

### The Right way :

In a truely modular system when you add the `comments`, you should NOT go and touch the code within the `users` or `blog` module.
(Remember the `open-closed` principle in `SOLID` ?!)

Imagine you are in a team and each member is working on a seprate module.

`Blog` module is not yours. your team mate is responsible for it and is allowed to code on it.

But when you want to start to define the eloquent relations between `Comment` and `User` and `Article` models. you immidiately realize that you have to put code on the eloquent models to define the inverse of the relationships. Crap ! 

We have to touch the code of both `Blog` and `User` module.

for example : You have to open `User.php` and define the `public function comments() { return $this->hasMany(Comment::class); }`

So what to do ?!

How can `Comment` be introduced to the system without the user realizing it ?!
