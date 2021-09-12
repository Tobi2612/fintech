**Register New User**
----
  Returns json data about a single new user.

* **URL**

  /register

* **Method:**

  `POST`
  

* **Data Params**

  **Required:**

  request body {

    first_name: 'required|string',
    last_name: 'required|string',
    username: 'required|string',
    email: 'required|email',
    password: 'required|string',
    pin: 'required|integer|4 digits',

    }

* **Success Response:**

  * **Code:** 200 <br />
    **Content:** `{ id : 12, name : "Michael Bloom" }`
 
* **Error Response:**

  * **Code:** 404 NOT FOUND <br />
    **Content:** `{ error : "User doesn't exist" }`

  OR

  * **Code:** 401 UNAUTHORIZED <br />
    **Content:** `{ error : "You are unauthorized to make this request." }`


**Register New User**
----
  Returns json data about a single new user.


| Title | Register User |
| --- | --- |
| Url | `/register` |
| Method | `POST` |
| Data Params | **Required:**  `first_name: string`,`last_name: string`, `username: string`,`email: email`,`password: string`,`pin: integer|4 digits`|


  ```