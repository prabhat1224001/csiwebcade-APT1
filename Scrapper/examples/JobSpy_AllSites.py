from jobspy import scrape_jobs
import pandas as pd
import logging

# Configure logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')

def scrape_and_export_jobs(search_term="software engineer", location="mumbai", results_wanted=20, country_indeed="india", proxy=None):
    try:
        # Scrape jobs
        logging.info("Starting job scraping...")
        jobs: pd.DataFrame = scrape_jobs(
            site_name=["indeed", "linkedin", "zip_recruiter", "glassdoor"],
            search_term=search_term,
            location=location,
            results_wanted=results_wanted,  # be wary the higher it is, the more likely you'll get blocked (use rotating proxy if needed)
            country_indeed=country_indeed,
            proxy=proxy,  # Optional proxy argument, add as required
        )

        # Check if jobs DataFrame is not empty
        if not jobs.empty:
            # Formatting for pandas
            pd.set_option("display.max_columns", None)
            pd.set_option("display.max_rows", None)
            pd.set_option("display.width", None)
            pd.set_option("display.max_colwidth", 50)  # Adjust column width for better display

            # Output to console
            print(jobs)

            # Output to .csv
            csv_file = "./jobs.csv"
            jobs.to_csv(csv_file, index=False)
            logging.info(f"Outputted to {csv_file}")

            # Output to .xlsx (Excel)
            excel_file = "./jobs.xlsx"
            jobs.to_excel(excel_file, index=False)
            logging.info(f"Outputted to {excel_file}")

        else:
            logging.warning("No jobs found for the given parameters.")

    except Exception as e:
        logging.error(f"An error occurred: {e}")

if __name__ == "__main__":
    # Example use
    scrape_and_export_jobs(
        search_term="software engineer",
        location="mumbai",
        results_wanted=20,  # You can adjust this value as needed
        country_indeed="india",
        # Optional proxy usage
        # proxy="http://your-proxy:port"
    )
