import firebase_admin
from firebase_admin import credentials
from firebase_admin import db
import json
import scrape

#Fetch the service account key JSON file contents
cred = credentials.Certificate('se-internship-database-firebase-adminsdk-10szo-64b63bdb76.json')
#Initialize the app with a service account, granting admin privileges
firebase_admin.initialize_app(cred, {
    'databaseURL': "https://se-internship-database-default-rtdb.firebaseio.com/"
})







ref = db.reference("/")
users_ref = ref.child('user')

with open("database.json", "r") as f:
    file_contents = json.load(f)
#ref.set(file_contents)

db_ref = ref.child('internships')
db_ref.set(file_contents)