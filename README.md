# Laravel Larrock CMS :: Discount Component

---

#### Depends
- fanamurov/larrock-core
- fanamurov/larrock-catalog
- fanamurov/larrock-category

## INSTALL

1. Install larrock-core, larrock-catalog, larrock-category
2. Install larrock-discount
  ```sh
  composer require fanamurov/larrock-discount
  ```

4. Add the ServiceProvider to the providers array in app/config/app.php
  ```
  //LARROCK COMPONENT DISCOUNT DEPENDS
  \Larrock\ComponentDiscount\LarrockComponentDiscountServiceProvider::class
  ```

5. Publish views, migrations etc.
  ```sh
  $ php artisan vendor:publish
  ```
  Or
  ```sh
  $ php artisan vendor:publish --provider="Larrock\ComponentDiscount\LarrockComponentDiscountServiceProvider"
  ```
       
6. Run artisan command:
  ```sh
  $ php artisan larrock:check
  ```
  And follow the tips for setting third-party dependencies
  
  
7. Run migrations
  ```sh
  $ php artisan migrate
  ```

##START
http://yousite/admin/reviews