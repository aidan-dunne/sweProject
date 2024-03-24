from serpapi import GoogleSearch
import json


def jobScrape():
    params = {
        'api_key': '5121d893caf92b7ccd439e36f6643ed5d781a31971d9dffe702e21d5003fc2ba',
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
    # citizen determines if the job needs the applicant to be a US citizen
    citizen = False
    # underclassman determines if the applicant can be a freshman or sophomore
    underclassman = False

    jobList =[]
    companyList =[]
    locationList=[]
    underList=[]
    citizenList=[]

    search = GoogleSearch(params).get_dict()['jobs_results']  # where data extraction happens on the SerpApi backend

    # going through each job...
    for items in search:
        # check to see if the job is necessarily an internship
        # google job search is not amazing with this,
        # so i'll make sure to filter the actual jobs out and just output the internships or co ops
        if ('intern' in items.get('title').lower() or 'internship' in items.get('title').lower()
                or 'co-op' in items.get('title').lower()):
            # if so, extract title, name, and location
            jobTitle = items.get('title')
            jobList.append(jobTitle)

            companyName = items.get('company_name')
            companyList.append(companyName)

            location = items.get('location')
            locationList.append(location)

            qualifications = items.get('job_highlights')[0].get('items')
            # now, check the qualifications to see if the student needs to be a citizen or can be an underclassman\
            for x in range(len(qualifications)):
                if 'Citizenship' in qualifications[x] or 'Citizen' in qualifications[x]:
                    citizen = False
                    citizenList.append(citizen)
                else:
                    citizen = True
                    citizenList.append(citizen)

                if 'Junior' in qualifications[x] or 'Senior' in qualifications[x]:
                    underclassman = False
                    underList.append(underclassman)

                else:
                    underclassman = True
                    underList.append(underclassman)


            # printing out for testing reasons
            print(jobTitle)
            print(companyName)
            print(location)
            print(qualifications)
            print(citizen)
            print(underclassman)
            print(' ')

            object = {
                'job name': jobTitle,
                'company': companyName,
                'location': location,
                'citizenship': citizen,
                'underclassman': underclassman
            }
            objectList.append(object)


        else:
            break

    with open('database.json', 'w') as f:
        json.dump(objectList, f, indent= 2)


    return


def main():
     jobScrape()




if __name__ == '__main__':
    main()