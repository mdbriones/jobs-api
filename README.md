## Routes

- POST /jobs
create job details and scrape data

> sample Data: 
    {
        "urls" : ["https://www.worldometers.info/coronavirus/"],
        "css_selectors" : [".maincounter-number"]
    }

- GET /jobs/jobs
> get all data(keys) from redis

- GET /api/jobs/{id}
> get a specific record from redis using id(key)

- DELETE /api/jobs/{id}
> delete a record from redis using id(key)

- DELETE /api/jobs
> delete all records from redis