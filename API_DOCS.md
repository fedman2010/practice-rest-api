#No authentication required endpoints
___
##Authentication endpoint

Returns json data with collection of items.

* **URL**

  /login

* **Method:**

  `POST`

*  **URL Params**

   None

   **Required:**

   None

* **Data Params**

  ```
  {
    "username": "your-username",  
    "password": "your-password"
  }
  ```

* **Success Response:**

    * **Code:** 200 <br />
      **Content:** `[]`
      
* **Error Response:**

    * **Code:** 401 UNAUTHORIZED <br />
      **Content:** `{ "error": "Invalid credentials." }`

* **Sample Call:**

  ```
  curl --location --request POST 'http://secure-storage.localhost:8000/login' --header 'Content-Type: application/json' --data-raw '{ "username": "john", "password": "maxsecure1" }'
  ```

* **Notes**

This endpoint authenticates and returns cookie with PHPSESSION token.
Use this cookie in further requests to `Authentication required endpoints`

#Authentication required endpoints
___
##Get Item LIst

Returns json data with collection of items.

* **URL**

  /item

* **Method:**

  `GET`

*  **URL Params**

    None
   
   **Required:**
    
    None

* **Data Params**
    
    None

* **Success Response:**

    * **Code:** 200 <br />
      **Content:**
      ```
      [
          {
              "data": "very secure new item data",
              "created_at": {
                  "date": "2021-01-20 12:33:44.000000",
                  "timezone_type": 3,
                  "timezone": "UTC"
              },
              "updated_at": {
                  "date": "2021-01-20 12:33:44.000000",
                  "timezone_type": 3,
                  "timezone": "UTC"
                  }
          }
      ]
      ```

* **Error Response:**

    * **Code:** 401 UNAUTHORIZED <br />
      **Content:** `{"error":"Full authentication is required to access this resource."}`

* **Sample Call:**

  ```
  curl --location --request GET 'http://secure-storage.localhost:8000/item' --header 'Cookie: PHPSESSID=17b2ade879ab83a9a7ac2010744352b7'
  ```
  
##Crete Item

Creates new item.

* **URL**

  /item

* **Method:**

  `POST`

*  **URL Params**
   
   **Required:**
    
    data=[alphanumeric]

* **Data Params**
    
    None

* **Success Response:**

    * **Code:** 200 <br />
      **Content:** `[]`

* **Error Response:**

    * **Code:** 401 UNAUTHORIZED <br />
      **Content:** `{"error":"Full authentication is required to access this resource."}`
      
      OR
    * **Code:** 400 Bad Request <br />
      **Content:** `{"error":"No data parameter"}`

* **Sample Call:**

  ```
  curl --location --request POST 'http://secure-storage.localhost:8000/item?data=new%20item%20secret' --header 'Cookie: PHPSESSID=17b2ade879ab83a9a7ac2010744352b7'
  ```
    
##Update Item

Updates item by id.

* **URL**

  /item

* **Method:**

  `PUT`

*  **URL Params**
   
   **Required:**
    
    data=[alphanumeric],id=[integer]

* **Data Params**
    
    None

* **Success Response:**

    * **Code:** 200 <br />
      **Content:** `[]`

* **Error Response:**

    * **Code:** 401 UNAUTHORIZED <br />
      **Content:** `{"error":"Full authentication is required to access this resource."}`
      
      OR
    * **Code:** 400 Bad Request <br />
      **Content:** `{"error":"No id parameter"}`      
      OR
    * **Code:** 400 Bad Request <br />
      **Content:** `{"error":"No data parameter"}`      
      OR
    * **Code:** 400 Bad Request <br />
      **Content:** `{"error":"No item"}`

* **Sample Call:**

  ```
  curl --location --request PUT 'http://secure-storage.localhost:8000/item?id=1&data=new%20secret' --header 'Cookie: PHPSESSID=17b2ade879ab83a9a7ac2010744352b7'
  ```
      
##Delete Item

Deletes item by id.

* **URL**

  /item

* **Method:**

  `DELETE`

*  **URL Params**
   
   **Required:**
    
    id=[integer]

* **Data Params**
    
    None

* **Success Response:**

    * **Code:** 200 <br />
      **Content:** `[]`

* **Error Response:**

    * **Code:** 401 UNAUTHORIZED <br />
      **Content:** `{"error":"Full authentication is required to access this resource."}`
      
      OR
    * **Code:** 400 Bad Request <br />
      **Content:** `{"error":"No id parameter"}`
      
      OR
    * **Code:** 400 Bad Request <br />
      **Content:** `{"error":"No item"}`

* **Sample Call:**

  ```
  curl --location --request DELETE 'http://secure-storage.localhost:8000/item?id=1' --header 'Cookie: PHPSESSID=17b2ade879ab83a9a7ac2010744352b7'
  ```
  

