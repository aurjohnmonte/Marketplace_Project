THIS IS THE SELLER FOLDER.

The pages folder is where all the pages is stored like: dashboard, messages, notification etc.

to add a route like when user want to navigate to the notification page/component:

1. go to index.js (the router file). and add an element in routes variable.
2. then go to web.php in routes (system_app/routes/web.php). Find the seller Route and in the whereIn function at the bottom add the name component in the 2nd element which is an array.

eg:
    path: "/seller/notification" in vue route

    whereIn("p", ["notification"]); in laravel web.php

the purpose is so that when user want to reload it wont return 404 status.

