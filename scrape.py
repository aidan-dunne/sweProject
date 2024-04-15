from serpapi import GoogleSearch
import json
import os
from dotenv import load_dotenv
import requests
from requests.exceptions import ConnectionError
import datetime
from datetime import timedelta
load_dotenv()
APIKEY = os.getenv('API_KEY')


def jobScrape():
    objectList = []
    jobSearch = ['kansas city', 'kansas city', 'st. louis', 'st. louis', 'chicago', 'chicago', 'iowa', 'iowa']

    incrementer = 0
    for x in jobSearch:
        strsearch = "Computer Science Internships" + x
        if incrementer % 2 == 0:
            params = {
                'api_key': APIKEY,
                # https://serpapi.com/manage-api-key
                'engine': 'google_jobs',  # SerpApi search engine
                'gl': 'us',  # country of the search
                'hl': 'en',  # language of the search
                'q': strsearch,  # search query
            }
        elif incrementer % 2 == 1:
            params = {
                'api_key': APIKEY,
                # https://serpapi.com/manage-api-key
                'engine': 'google_jobs',  # SerpApi search engine
                'gl': 'us',  # country of the search
                'hl': 'en',  # language of the search
                'start': '10',  # page of start
                'q': strsearch,  # search query
            }
        incrementer += 1

        # variables to store all database information

        jobTitle = ''
        companyName = ''
        location = ''
        desc = ''
        link = ''
        # citizen determines if the job needs the applicant to be a US citizen
        citizen = False
        # underclassman determines if the applicant can be a freshman or sophomore
        underclassman = False

        search = GoogleSearch(params).get_dict()['jobs_results']  # where data extraction happens on the SerpApi backend
        dummy = 0
        # going through each job...
        for items in search:

            # check to see if the job is necessarily an internship
            # google job search is not amazing with this,
            # so i'll make sure to filter the actual jobs out and just output the internships or co ops

            # these are parameters that will be used later
            checkI = 0
            checkU = 0

            if ('intern' in items.get('title').lower() or 'internship' in items.get('title').lower() or
                    'internships' in items.get('title').lower() or 'co-op' in items.get('title').lower() and
                    '2020' not in items.get('title') and '2021' not in items.get('title') and
                    '2022' not in items.get('title') and '2023' not in items.get('title')):


                # if so, extract title, name, and location

                jobTitle = items.get('title')
                companyName = items.get('company_name')
                location = items.get('location')
                qualifications = items.get('job_highlights')[0].get('items')
                apply = items.get('related_links')[0].get('link')
                date = items.get('detected_extensions').get('posted_at')

                pay = items.get('detected_extensions').get('salary')
                remoteOption = False
                part = False
                full = False

                # now, check the qualifications to see if the student needs to be a citizen or can be an underclassman
                # if any of the qualifications have either "citizenship" or anything referring to
                # needing to be an upperclassman, add one to the count. If the count
                # is anything but 0, the flag is marked as FALSE; meaning
                # that you either must be a citizen or an upperclassman

                for y in range(len(qualifications)):
                    if 'Citizenship' in qualifications[y] or 'Citizen' in qualifications[y]:
                        checkI += 1
                    else:
                        checkI += 0

                    if 'Junior' in qualifications[y] or 'Senior' in qualifications[y]:
                        checkU += 1

                    else:
                        checkU += 0

                if checkI != 0:
                    citizen = False
                else:
                    citizen = True

                if checkU != 0:
                    underclassman = False
                else:
                    underclassman = True

                if (items.get('detected_extensions').get('work_from_home') is None
                        or 'remote' in items.get('description').lower()):
                    remoteOption = False
                else:
                    remoteOption = True

                if ('web' in items.get('related_links')[0].get('text') and
                        'results' in items.get('related_links')[0].get('text')):
                     apply1 = 'https://' +(items.get('related_links')[0].get('text').replace('See web results for', '').replace(' ', '').lower()
                              + '.com')

                     apply2 = 'https://' +(items.get('related_links')[0].get('text').replace('See web results for', '').replace(' ', '_').lower()
                              + '.com')
                     apply3 = 'https://' +(items.get('related_links')[0].get('text').replace('See web results for', '').replace(' ', '').lower()
                              + '.org')
                     apply4 = 'https://' +(items.get('related_links')[0].get('text').replace('See web results for', '').replace(' ', '_').lower()
                              + '.org')
                     try:
                        status = requests.head(apply1)
                     except ConnectionError:
                        apply = apply
                        try:
                            status = requests.head(apply2)
                        except ConnectionError:
                            apply = apply
                            try:
                                status = requests.head(apply3)
                            except ConnectionError:
                                apply = apply
                                try:
                                    status = requests.head(apply4)
                                except ConnectionError:
                                    apply = apply
                                else:
                                    apply = apply4
                            else:
                                apply = apply3

                        else:
                            apply = apply2
                     else:
                         apply = apply1


                # printing out for testing reasons
                print(jobTitle)
                print(companyName)
                print(location)
                print(qualifications)
                print(citizen)
                print(apply)
                print(underclassman)
                if date is None:

                    print(date)


                else:
                    isTime = date[2:len(date)]
                    date = date[0:2]

                    todaysDate = datetime.datetime.now()

                    if 'hours' in isTime:
                        todaysDate = datetime.datetime.now()
                        fixedDate = todaysDate - timedelta(hours=int(date))
                    else:

                        fixedDate = todaysDate - timedelta(days=int(date))

                    print(fixedDate)
                    #date = fixedDate
                    date = str(fixedDate)



                if pay is None:
                    pay = 0
                    print(pay)
                else:
                    print(pay)
                print(remoteOption)
                print(' ')

                # after that, make a json formatted object to write to the json file
                dateFallBack = 0

                object = {
                    'job name': jobTitle,
                    'company': companyName,
                    'location': location,
                    'citizenship': citizen,
                    'underclassman': underclassman,
                    'remote': remoteOption,
                    'pay': pay,
                    'date_posted': date,
                    'link': apply
                }
                # then, make a list of json writeable text

                objectList.append(object)


            else:
                dummy= 0




    newObjectList =[]
    for elem in objectList:
        if elem not in newObjectList:
            newObjectList.append(elem)

    # finally, write the list of jsonable text to the json code to send to the file
    with open('database.json', 'w') as f:
          json.dump(newObjectList, f, indent= 2)


    return


def main():
     jobScrape()




if __name__ == '__main__':
    main()