from jobspy import scrape_jobs
import pandas as pd
import os
import time
from datetime import datetime

# Function to generate a unique filename using current time
def generate_csv_filename():
    timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
    return f"jobs_{timestamp}.csv"

# Set unique filename
csv_filename = generate_csv_filename()

# Parameters
results_wanted = 1000  # Total number of jobs you want
offset = 0  # Start offset for paginated results
all_jobs = []  # Store all scraped jobs
max_retries = 3  # Max retry attempts for failed requests
results_in_each_iteration = 30  # Number of jobs per request

# Start scraping
while len(all_jobs) < results_wanted:
    retry_count = 0

    while retry_count < max_retries:
        print(f"Scraping jobs from {offset} to {offset + results_in_each_iteration}...")

        try:
            # Scrape jobs
            jobs = scrape_jobs(
                site_name=["indeed"],
                search_term="software engineer",
                location="Los Angeles, CA",
                results_wanted=min(results_in_each_iteration, results_wanted - len(all_jobs)),
                country_indeed="USA",
                offset=offset,
                # proxy="http://your_proxy_here"
            )

            # Add scraped jobs to the list
            all_jobs.extend(jobs.to_dict('records'))

            # Increment offset for the next batch
            offset += results_in_each_iteration

            # Log progress
            print(f"Successfully scraped {len(all_jobs)} jobs so far.")

            # Introduce a reasonable delay between requests to avoid rate limiting
            print(f"Sleeping for {2 * (retry_count + 1)} seconds...")
            time.sleep(2 * (retry_count + 1))  # Sleep for 2 seconds

            # Break retry loop on successful request
            break
        except Exception as e:
            # Log error and retry after a delay
            print(f"Error occurred: {e}")
            retry_count += 1
            delay = 100 * retry_count
            print(f"Retrying in {delay} seconds...")
            time.sleep(delay)

            # Stop if max retries reached
            if retry_count >= max_retries:
                print("Max retries reached for this iteration. Moving on.")
                break

# Convert the list of job data to a DataFrame
if all_jobs:
    jobs_df = pd.DataFrame(all_jobs)

    # Output formatting
    pd.set_option("display.max_columns", None)
    pd.set_option("display.max_rows", None)
    pd.set_option("display.width", None)
    pd.set_option("display.max_colwidth", 50)

    # Output to console
    print(jobs_df)

    # Save DataFrame to CSV
    jobs_df.to_csv(csv_filename, index=False)
    print(f"Outputted to {csv_filename}")

else:
    print("No jobs were scraped.")
