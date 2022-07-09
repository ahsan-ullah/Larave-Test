
# About Laravel-Test
Show posts in the feed with their information (Image, description, date, author)
including total likes and the last 5 usernames who liked the post.
- Feed is public (Doesn’t need authentication), paginated, and order by creationdate.
- Users should be authenticated to create or like/unlike posts.
- Users can remove their posts, with the image file.
- Users can like/unlike other posts.
- Users can see all likes of a specific post.
- Send a notification to other users when a new post is added. (Databasechannel)
- Automatically delete posts 15 days old.
- Push it in GitHub.
    - Repository name should be {your-name}-Laravel-Test.
    - Push it in development branch
    - Merge it in master branch from development branch.
- Note: Don’t push directly in master.

## I used
- Laravel 9.19
- Notification
- Queue
- Task Scheduling
- JWT Authentication
## How to Setup and RUN
1. Pull the repository form 
https://github.com/ahsan-ullah/Larave-Test.git

## Run the folloing command
> composer update
> cp .env.example .env
- Create a database by laravel_test
- Update .env file database name to laravel_test
> php artisan key:generate

> php artisan jwt:secret

> php artisan migrate

> php artisan serve

> php artisan storage:link

> php artisan queue:listen

> php artisan schedule:work

## Postman Collection
Postman Collection Link:
[Import Postmean Collection](https://www.getpostman.com/collections/c69ac03e69ca2dd85fcc)

## Postman Collection Documentaiont
Postman Collection Link:
[Collection Documentaiont](https://documenter.getpostman.com/view/1952071/UzJPLv1A)
## Dependencies
- PHP 8 or hire
## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
