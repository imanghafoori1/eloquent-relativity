#Eloquent Relativity

This allows you to decouple your eloquent models from one another, so it you are going to design a modular laravel application, it would be truely modular.

## A problem stops true modularity :

let face it, imaging you have a blog application.

then you want to add a comment feature onto it, so that your users can comment on your posts.

In a modular structure, you have a `blog` module which know and depends upon the `user` module.

but the `user` module should not know or care about the `post` module. that is a `plug-in` on the top of the `user` module.

now we want to add a `comment` module, on the top of `user` and `blog` module.

In a truely modular system when you add the `comments`, you should NOT go and touch the code within the `users` or `blog` module.

Imaging you are a team and each person is working on a seprate module.

`Blog` module is not yours. your team mate is responsible and is allowed to code on it.

But when you want to start to define the eloquent relations between `Comment` and `User` and `Article` models. you immidiately realize that you have to put code on the eloquent models to define the inverse of the relationships. Crap !

So what to do ?!
