# LaraPoints
LaraPoints is a REST API skeleton using Laravel framework.
# LaraPoints project goal
The goal of this project is to create a ready to use REST API skeleton to help developers get their API's up and runining and focus on business logic implementation instead of investing time implementing the commonly needed endpoints and functionalities.

# How to use

**Installing the project:**

- clone the project on your machine: 

<br/>
<br/>

```shell script
$ git clone https://github.com/ihamzehald/larapoints.git
```

<br/>
<br/>

- cd to the project directory (larapoints) and Install the required libraries using composer:

<br/>
<br/>

```shell script
$ cd larapoints
```

```shell script
$ composer install
```

<br/>


- Configure your environment:
<br/>
<br/>
Rename .env.example to .env
<br/>
<br/>
Configure your database connection by changing the values of the following variables:
<br/>
<br/>
DB_DATABASE={ Your DB name }
<br/>
DB_USERNAME={ Your DB user name }
<br/>
DB_PASSWORD={ Your DB password }
<br/>
<br/>

**Supporting API key**  _(Recommended for mobile applications and private APIs)_ :

<br/>
<br/>
If you are developing an API for a mobile application or your API is private your API client should 
send API key using x-api-key header, this is disabled by default, to enable that change the following variables values.
<br/>
<br/>
First change ACTIVATE_API_KEY=false to ACTIVATE_API_KEY=true
<br/>
<br/>
Then change the value of API_KEY to your secured API key.
<br/>
<br/>
This tool is recommended to generate secure API key, it is recommended to generate a complex key  greater than or equal to 64 characters.
<br/>
<br/>
https://passwordsgenerator.net
<br/>
<br/>
Then share this key securely with your API clients.
<br/>
<br/>
Note: once ACTIVATE_API_KEY set to true, all your API clients should send  (x-api-key)  header and it's value should match API_KEY.
<br/>
<br/>
Apply the database migrations:

<br/>
<br/>

```shell script
$ php artisan migrate
```

Configure your MailGun integration by changing the values of the following variables:
<br/>
<br/>
MAIL_USERNAME={ Your MailGun username}
<br/>
MAIL_PASSWORD={ Your MailGun password}
<br/>
MAIL_FROM_ADDRESS={ Your from address }
<br/>
<br/>
Change the value of JWT_SECRET to a private JWT secret key.
<br/>
<br/>
- Configure your web server
<br/>
<br/>
Apache2 virtual host:
<br/>
<br>
Note: this is a sample Apache2 virtual host for local testing, you should always configure your virtual hosts with SSL, but this is out of this documentation scope.
<br/>
<br/>

```shell script
$ cd /etc/apache2/sites-available
```

<br/>
Create the a virtual host config file (larapoints.loc.conf) for example.
<br/>

**larapoints.loc.conf** sample:
<br/>
<br/>


```shell script

<VirtualHost *:80>

    DocumentRoot "/var/www/html/larapoints/public/"
    ServerName larapoints.loc
    ErrorLog ${APACHE_LOG_DIR}/larapoints.loc.error.log
    CustomLog ${APACHE_LOG_DIR}/larapoints.loc.access.log combined

    <Directory /var/www/html/larapoints/public/>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Order allow,deny
                allow from all
    </Directory>

</VirtualHost>
```

<br/>

Enable larapoints virtual host:

<br>

```shell script
$ a2ensite larapoints.loc.conf 
```

<br/>

Add the virtual host server name to the hosts file (/etc/hosts):
<br/>
<br/>

```shell script
127.0.1.1       larapoints.loc
```



# Available endpoints

**Auth:**

- /auth/jwt/login
- /auth/jwt/logout
- /auth/jwt/refresh
- /auth/jwt/password/request/reset
- /auth/jwt/password/otp/verify
- /auth/jwt/password/reset

<br/>

**User:**

- /user/me


# API versioning

There is a lot of methods to support versioning in your API, LaraPoints use URI versioning and directory structure and OOP inheritance to support backward compatibility and DRY principle when it is possible.

<br/>

- Directory structure and namespaces:
<br/>
<br/>
To support versioning the application main controllers places in a directory with the same verizon name (v1 for example) to handle v1 API implementation under the following path (app/Http/Controllers/API/V1), if you want to implement V2 of your API for example you create V2 folder in (app/Http/Controllers/API) directory path and make your implementation there.
<br/>
<br/>
All namespaces of your controllers should have a namespace related to the directory version of your API.
<br/>
<br/>
Example : namespace App\Http\Controllers\API\V1;
<br/>
<br/>

- Routing:
<br/>
<br/>
To support URI versioning LaraPoints use the following methodology :  
<br/>
1- In routes folder there is a new folder created (api) and inside this folder the routes distributed based on the API version in a file for each version, v1.php for version one for example.
<br/>
<br/>
2- RouteServiceProvider.php file modified to load the routes on the previous step and inject the appropriate namespace for each version.
<br/>
<br/>

- V2 implementation:

<br/>
<br/>
When you face breaking changes, you will have to release a new version of your API, LaraPoints already contains a suggested implementation of V2, this implementation placed under (app/Http/Controllers/API/V2), this implementation supporting backward compatibility and applying DRY principle when it is possible, you can use the same methodology or use your own way that fits your application needs better.

<br/>
<br/>

# API Documentation

- <b> Postman </b> :

When it comes to an API documentation it is recommended to use Postman, it is highly powerful and flexible tool and easy to implement and one of the best tools to test your API.

<br/>

LaraPoints has available Postman implementation ready to use here :

<be/>

https://documenter.getpostman.com/view/3215735/SzRxXWdP?version=latest


- <b> Swagger UI </b>:

LaraPoints supports Swagger UI using OpenApi tags as the following:
- API documentation path:
<br/>
http://larapoints.loc/api/documentation
<br>
<br/>
- Auto generating API documentation:
<br/>
LaraPoints integrated with L5-Swagger library to auto generate the documentation of the API endpoints using Open API Annotations.
<br/>
<br/>

**Example:**

<br/>

**GET request:**
<br/>

```shell script
    /**
     * Get a user profile
     *
     * @return mixed
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Get(
     *   path="/user/me",
     *   tags={"User"},
     *   summary="Get the authenticated User",
     *   description="Get the authenticated User",
     *   operationId="UserMe",
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="Response data",
     *                         ref="#/components/schemas/User"
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                     example={
     *                         "message": "JWT token refresh successfully",
     *                         "data": {
     *                                      "id": 1,
     *                                      "name": "Hamza al darawsheh",
     *                                      "email": "ihamzehald@gmail.com",
     *                                      "email_verified_at": null,
     *                                      "created_at": "2020-03-20T09:10:32.000000Z",
     *                                      "updated_at": "2020-05-08T20:39:06.000000Z"
     *                                  },
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         description="response errors",
     *                         @OA\Property(
     *                              property="unauthorized",
     *                              type="string",
     *                              description="Unauthorized error message",
     *                          ),
     *                     ),
     *                     example={
     *                         "message": "Unauthorized",
     *                         "data": null,
     *                         "errors": {
     *                                      "unauthorized": "Unauthorized request"
     *                                  }
     *
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */
```

**POST request:**
<br/>

```shell script
    /**
     * Get a JWT via given credentials.
     *
     * @return mixed
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/auth/jwt/login",
     *   tags={"Auth"},
     *   summary="JWT login",
     *   description="Login a user and generate JWT token",
     *   operationId="jwtLogin",
     *   @OA\RequestBody(
     *       required=true,
     *       @OA\MediaType(
     *           mediaType="application/json",
     *           @OA\Schema(
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   description="User email",
     *                   type="string",
     *                   example="ihamzehald@gmail.com"
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   description="User password",
     *                   type="string",
     *                   example="larapoints123"
     *               ),
     *           )
     *       )
     *   ),
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="Response data",
     *                          @OA\Property(
     *                              property="access_token",
     *                              type="string",
     *                              description="JWT access token",
     *                              ),
     *                          @OA\Property(
     *                              property="token_type",
     *                              type="string",
     *                              description="Token type"
     *                              ),
     *                          @OA\Property(
     *                              property="expires_in",
     *                              type="integer",
     *                              description="Token expiration in miliseconds",
     *                              ),
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="null",
     *                         description="response errors",
     *                     ),
     *                     example={
     *                         "message": "User logged in successfully",
     *                         "data": {
     *                                      "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *                                      "token_type": "bearer",
     *                                      "expires_in": 3600
     *                                  },
     *                         "errors": null
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *    @OA\Response(
     *         response="401",
     *         description="Unauthorized",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(
     *                    @OA\Property(
     *                         property="message",
     *                         type="string",
     *                         description="Response message",
     *                     ),
     *                    @OA\Property(
     *                         property="data",
     *                         type="null",
     *                         description="Response data",
     *                     ),
     *                  @OA\Property(
     *                         property="errors",
     *                         type="object",
     *                         description="response errors",
     *                         @OA\Property(
     *                              property="wrong_credentials",
     *                              type="string",
     *                              description="Wrong credentials error message",
     *                          ),
     *                     ),
     *                     example={
     *                         "message": "Wrong credentials",
     *                         "data": null,
     *                         "errors": {
     *                                      "wrong_credentials": "The provided credentials don't match our records"
     *                                  }
     *
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *     security={
     *         {"bearerJWTAuth": {}}
     *     }
     * )
     */
```


**Model example:**
<br/>

```shell script
/**
 * @OA\Schema(@OA\Xml(name="User"))
 * 
 * @OA\Property(
 *   property="id",
 *   type="string",
 *   description="User ID"
 * )
 *
 * @OA\Property(
 *   property="name",
 *   type="string",
 *   description="User name"
 * )
 * 
 * @OA\Property(
 *   property="email",
 *   type="string",
 *   description="User email"
 * )
 * 
 * @OA\Property(
 *   property="email_verified_at",
 *   type="string",
 *   description="Email verified at"
 * )
 * 
 * @OA\Property(
 *   property="created_at",
 *   type="string",
 *   description="Created at"
 * )
 * 
 * @OA\Property(
 *   property="updated_at",
 *   type="string",
 *   description="Updated at"
 * )
 * 
 */
```
**To generate the API documentation apply the following command:**
```shell script
$ php artisan l5-swagger:generate
```
