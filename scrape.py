from serpapi import GoogleSearch
import json
import os
from dotenv import load_dotenv
load_dotenv()
APIKEY = os.getenv('API_KEY')

def jobScrape():

    params = {
        'api_key': APIKEY,
        # https://serpapi.com/manage-api-key
        'engine': 'google_jobs',  # SerpApi search engine
        'gl': 'us',  # country of the search
        'hl': 'en',  # language of the search
        'q': 'Computer Science Internships',  # search query
    }

    objectList = []

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
                'internships' in items.get('title').lower() or 'co-op' in items.get('title').lower()):

            # if so, extract title, name, and location

            jobTitle = items.get('title')
            companyName = items.get('company_name')
            location = items.get('location')
            qualifications = items.get('job_highlights')[0].get('items')
            link = items.get('related_links')[0].get('link')

            # now, check the qualifications to see if the student needs to be a citizen or can be an underclassman
            # if any of the qualifications have either "citizenship" or anything referring to
            # needing to be an upperclassman, add one to the count. If the count
            # is anything but 0, the flag is marked as FALSE; meaning
            # that you either must be a citizen or an upperclassman

            for x in range(len(qualifications)):
                if 'Citizenship' in qualifications[x] or 'Citizen' in qualifications[x]:
                    checkI += 1
                else:
                    checkI += 0

                if 'Junior' in qualifications[x] or 'Senior' in qualifications[x]:
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


            # printing out for testing reasons
            print(jobTitle)
            print(companyName)
            print(location)
            print(qualifications)
            print(citizen)
            print(underclassman)
            print(link)
            print(' ')

            # after that, make a json formatted object to write to the json file

            object = {
                'job name': jobTitle,
                'company': companyName,
                'location': location,
                'citizenship': citizen,
                'underclassman': underclassman
            }
            # then, make a list of json writeable text

            objectList.append(object)


        else:
            dummy= 0


    # finally, write the list of jsonable text to the json code to send to the file
   # with open('database.json', 'w') as f:
      #  json.dump(objectList, f, indent= 2)


    return


def main():
     jobScrape()




if __name__ == '__main__':
    main()