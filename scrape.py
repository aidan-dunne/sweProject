from serpapi import GoogleSearch
import json
import os
from dotenv import load_dotenv
import requests
from requests.exceptions import ConnectionError
import datetime
from datetime import timedelta
import re
load_dotenv()
APIKEY = os.getenv('API_KEY')


def jobScrape():
    # initializing empty object list in order to populate a unique one
    objectList = []
    qual_flag = True
    # these are our current locations for searching, this can change
    jobSearch = ['kansas city',  'st. louis', 'chicago',  'iowa', ]
    # this is to get the current year for searching
    thisYear = datetime.date.today()
    # incrementer goes between even and odd numbers, changing the page count from 1 to 2 on every other search
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

        search = GoogleSearch(params).get_dict()# where data extraction happens on the SerpApi backend
        search = search['jobs_results']
        # going through each job...
        for items in search:

            # check to see if the job is necessarily an internship
            # google job search is not amazing with this,
            # so i'll make sure to filter the actual jobs out and just output the internships or co ops

            # these are parameters that will be used later
            checkI = 0
            checkU = 0

            # these two parts make sure the internship is an internship, and if a year is noted, is in the current year.
            # i've also made it so it checks for the next year as well.
            if ('intern' in items.get('title').lower() or 'internship' in items.get('title').lower() or
                    'internships' in items.get('title').lower() or 'co-op' in items.get('title').lower()):
                validYear = re.match(r'.*(2[0-9]{3})', items.get('title'))

                if (validYear is None or (int(validYear.group(1)) if validYear else '') == int(thisYear.year) or
                        (int(validYear.group(1)) if validYear else '') == int(thisYear.year) + 1):



                    # if so, extract title, name, and location

                    jobTitle = items.get('title')
                    companyName = items.get('company_name')
                    location = items.get('location')
                    qualifications = items.get('job_highlights')
                    if qualifications and len(qualifications) > 0:
                        qualifications = qualifications[0].get('items')
                        if qualifications is not None:
                            qualifications = items.get('job_highlights')[0].get('items')
                        else:
                            qual_flag = False
                    else:
                        qual_flag = False
                    apply = items.get('apply_options')[0].get('link')
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
                    if qual_flag == True:
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

                    # this makes the links that are unreadable possibly readable
                    # to be clear, if the link links to a google search, then this is my attempt at formatting
                    # it as the actual company. what i've done is checked to see if each iteration of url
                    # (that being spaces, underscores, .com and .org)
                    # goes to a valid address. if not, i've made it just google search.

                    if ('web' in items.get('apply_options')[0].get('title') and
                            'results' in items.get('apply_options')[0].get('title') and items.get('apply_options'[0] != None)):
                         apply1 = 'https://' +(items.get('apply_options')[0].get('title').replace('See web results for', '').replace(' ', '').lower()
                                  + '.com')

                         apply2 = 'https://' +(items.get('apply_options')[0].get('title').replace('See web results for', '').replace(' ', '_').lower()
                                  + '.com')
                         apply3 = 'https://' +(items.get('apply_options')[0].get('title').replace('See web results for', '').replace(' ', '').lower()
                                  + '.org')
                         apply4 = 'https://' +(items.get('apply_options')[0].get('title').replace('See web results for', '').replace(' ', '_').lower()
                                  + '.org')
                         try:
                            status = requests.head(apply1)
                         except ConnectionError:
                            apply = apply + ' ' + companyName
                            try:
                                status = requests.head(apply2)
                            except ConnectionError:
                                apply = apply + ' ' + companyName
                                try:
                                    status = requests.head(apply3)
                                except ConnectionError:
                                    apply = apply + ' ' + companyName
                                    try:
                                        status = requests.head(apply4)
                                    except ConnectionError:
                                        apply = apply + ' ' + companyName
                                    else:
                                        apply = apply4
                                else:
                                    apply = apply3

                            else:
                                apply = apply2
                         else:
                             apply = apply1


                    # printing out all the things to make sure that the scaper is working as intended
                    print(jobTitle)
                    print(companyName)
                    print(location)
                    print(qualifications)
                    print(citizen)
                    print(apply)
                    print(underclassman)

                    # here, i am formatting date. if none was listed, keep it as none
                    if date is None:

                        print(date)

                    # otherwise, see what the number was,
                    # and then check to see if the period of time is hours or days
                    # then, depending on which was which, assign the correct datetime to that
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
                        date = str(fixedDate)


                    # similar thing with pay
                    if pay is None:
                        pay = 0
                        print(pay)
                    else:
                        print(pay)
                    print(remoteOption)
                    print(' ')

                    # after that, make a json formatted object to write to the json file


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
                    # once we're done with all of this, append it to the empty list
                    objectList.append(object)

                else:
                    pass

            else:
                pass



    # finally, create an object list that we can add more results to
    # if there are any identical listings (which sometimes happens)
    # just check to see if the object is already stored
    # if it is not, put it in the list
    newObjectList =[]
    for elem in objectList:
        if elem not in newObjectList:
            newObjectList.append(elem)

    # this is commented out for now, but if there comes a time in which a user finds a
    # listing that was not caught by the scraper, they can add this block of code to add it
    # it is important to note that this can be copy and pasted for however many listings are wanting to be added
    # this is also outside of the loop, so it will not be overwritten.

    # manualObject = {
    #     'job name': 'JobName',
    #     'company': 'CompanyName',
    #     'location': 'City, State',
    #     'citizenship': False,
    #     'underclassman': False,
    #     'remote': False,
    #     'pay': 'pay a month/year',
    #     'date_posted': 'year-month-day time',
    #     'link': 'linkapply.com'
    # }
    # newObjectList.append(manualObject)

    # finally, write the list of jsonable text to the json code to send to the file
    with open('database.json', 'w') as f:
          json.dump(newObjectList, f, indent= 2)


    return


def main():
     jobScrape()




if __name__ == '__main__':
    main()