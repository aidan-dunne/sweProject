import { initializeApp } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-app.js";
import { getDatabase, ref, set, get, onValue } from "https://www.gstatic.com/firebasejs/10.9.0/firebase-database.js";
// Your web app's Firebase configuration
const firebaseConfig = {
    apiKey: "AIzaSyCEs4fuVQHi2dwqnV6TJHSO1fZ6qx6kXc8",
    authDomain: "se-internship-database.firebaseapp.com",
    databaseURL: "https://se-internship-database-default-rtdb.firebaseio.com/",
    projectId: "se-internship-database",
    storageBucket: "se-internship-database.appspot.com",
    messagingSenderId: "520655988080",
    appId: "1:520655988080:web:573c2f7436e2acddf89bb5"
};

//Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const db = getDatabase(app);
  set(ref(db, "book/Book5"), {
    Author: "Andy",
    Genre: "horror",
    Cost: 100,
    Title: "andy",
  });
  // Basically the way this works is you get the ref you need and can set whatever you need
  // Setting overwrites anything else, but if you use the 'update' method you can just change specific fields
  // (Should be the same code otherwise);