from flask import Flask, render_template, request, redirect, url_for, send_file
import pandas as pd
from io import BytesIO
from jobspy import scrape_jobs

app = Flask(__name__)

# Home route to display the form
@app.route('/')
def index():
    return render_template('index.html')

# Route to handle form submission and display results
@app.route('/search', methods=['POST'])
def search():
    location = request.form['location']
    results_wanted = int(request.form['results_wanted'])
    country_indeed = request.form['country_indeed']

    # Scrape jobs
    jobs_df = scrape_jobs(
        site_name=["linkedin", "zip_recruiter", "glassdoor"],
        search_term="software engineer",
        location=location,
        results_wanted=results_wanted,
        country_indeed=country_indeed,
    )

    # Convert DataFrame to HTML table
    jobs_html = jobs_df.to_html(classes='table table-striped table-hover', index=False, escape=False, 
                                table_id="job-results")

    # Pass the data to results.html
    return render_template('results.html', location=location, results_wanted=results_wanted, 
                           country_indeed=country_indeed, jobs_html=jobs_html, jobs_df=jobs_df)

# Route to export data to Excel
@app.route('/export')
def export():
    location = request.args.get('location')
    results_wanted = int(request.args.get('results_wanted'))
    country_indeed = request.args.get('country_indeed')

    # Scrape jobs again for the export
    jobs_df = scrape_jobs(
        site_name=["linkedin", "zip_recruiter", "glassdoor"],
        search_term="software engineer",
        location=location,
        results_wanted=results_wanted,
        country_indeed=country_indeed,
    )

    # Convert DataFrame to Excel
    output = BytesIO()
    with pd.ExcelWriter(output, engine='xlsxwriter') as writer:
        jobs_df.to_excel(writer, sheet_name='Jobs', index=False)
    
    output.seek(0)
    return send_file(output, as_attachment=True, download_name='jobs.xlsx', mimetype='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')

if __name__ == '__main__':
    app.run(debug=True)
