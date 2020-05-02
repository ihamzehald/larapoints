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




# API Documentation
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
     * Get the authenticated User.
     * @return \Illuminate\Http\JsonResponse
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Get(
     *   path="/user/me",
     *   tags={"User"},
     *   summary="Get the authenticated User",
     *   description="Get the authenticated User",
     *   operationId="jwtMe",
     *  @OA\Response(
     *         response="200",
     *         description="ok",
     *         content={
     *             @OA\MediaType(
     *                 mediaType="application/json",
     *                 @OA\Schema(ref="#/components/schemas/User")
     *              )
     *         }
     *     ),
     *   @OA\Response(response="401",description="Unauthorized"),
     *  security={
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
     * @return \Illuminate\Http\JsonResponse
     *
     * Swagger UI documentation (OA)
     *
     * @OA\Post(
     *   path="/user/auth/jwt/login",
     *   tags={"User"},
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
     *                     @OA\Property(
     *                         property="access_token",
     *                         type="string",
     *                         description="JWT access token"
     *                     ),
     *                     @OA\Property(
     *                         property="token_type",
     *                         type="string",
     *                         description="Token type"
     *                     ),
     *                     @OA\Property(
     *                         property="expires_in",
     *                         type="integer",
     *                         description="Token expiration in miliseconds",
     *                         @OA\Items
     *                     ),
     *                     example={
     *                         "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
     *                         "token_type": "bearer",
     *                         "expires_in": 3600
     *                     }
     *                 )
     *             )
     *         }
     *     ),
     *   @OA\Response(response="401",description="Unauthorized"),
     * )
     */
```

**To generate the API documentation apply the following command:**
```shell script
$ php artisan l5-swagger:generate
````
