from bs4 import BeautifulSoup
from selenium import webdriver
import time

remote = False
miles = 20


type = input("Input job search: ").replace(' ', "+")
location = input("Input location: ").replace(' ', '+')
# if input("Are you interested in remote positions") == 'Y' or 'y' or 'Yes' or 'yes' or 'YES':
#     remote = True
# else:
#     remote = False
#
# if input("Input maximum distance from location (5, 10, 20, 50, 100): "):
#     remote = True





settings = webdriver.FirefoxOptions()
settings.add_argument('--headless')
url = webdriver.Firefox(options=settings)
if remote == True and miles != 20:
    remote = True
url.get("https://www.jobs.com/jobs/search?q=" + type + "&where=" + location)


time.sleep(3)

parse = url.page_source

look = BeautifulSoup(parse, features='html.parser')

jobs = look.find_all('h2', class_='card-title')
companies = look.find_all('h3', class_='card-company-name')
locations = look.find_all('span', class_='card-job-location')

counter = 1
jobList = []
companyList = []
locationList = []
for job in jobs:
    jobList.append("Job #" + str(counter) +": " + job.text)
    counter = counter + 1
for company in companies:
    companyList.append(company.text)

for located in locations:
    locationList.append(located.text)

for x in range(len(companyList)):
    print(jobList[x])
    print(companyList[x])
    print(locationList[x])
    print(" ")


url.quit()
