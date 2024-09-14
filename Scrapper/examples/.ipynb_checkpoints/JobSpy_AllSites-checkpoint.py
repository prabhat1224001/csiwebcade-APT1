from jobspy import scrape_jobs
import pandas as pd

# Scrape jobs from websites excluding Indeed
jobs: pd.DataFrame = scrape_jobs(
    site_name=["linkedin", "zip_recruiter", "glassdoor"],  # Removed "indeed"
    search_term="software engineer",
    location="Dallas, TX",
    results_wanted=25,  # Be cautious of the number to avoid getting blocked
    country_indeed="USA",  # This parameter might not be necessary if Indeed is excluded
    # proxy="http://jobspy:5a4vpWtj8EeJ2hoYzk@ca.smartproxy.com:20001",
)

# Formatting for pandas
pd.set_option("display.max_columns", None)
pd.set_option("display.max_rows", None)
pd.set_option("display.width", None)
pd.set_option("display.max_colwidth", 50)  # Set to 0 to see the full job URL/description

# Output to console
print(jobs)

# Output to .csv
jobs.to_csv("./jobs.csv", index=False)
print("Outputted to jobs.csv")

# Output to .xlsx (Excel)
jobs.to_excel("./jobs.xlsx", index=False)
print("Outputted to jobs.xlsx")
