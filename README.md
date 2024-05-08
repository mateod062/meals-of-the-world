# Meals of the world

### Laravel backend application to display meals in multiple languages

## API endpoint

### GET
```
 /api/meals/&per_page=?&page=?&lang=?&with=tags,ingredients,category&tags=?$diff_time=?
```

### Params: 
    - per_page (optional) example: 5
    - page (optional) example: 3
    - lang (required) example: en
    - with (optional) example: ingredients,tags,category
    - tags (optional) example: 1,5
    - diff_time (optional) example: 1492834

## How to run

### Clone the repository
```
git clone 
```

### Adjust the .env file
```
cp .env.example .env
```

### Run the migrations and fill the database
```
php artisan migrate
php artisan db:seed
```

### Run the server
```
php artisan serve
```
