<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dashboard</h3>
                    </div>
                    <div class="card-body">
                        <p>Welcome to CBAM Dashboard, {{ Auth::user()?->name ?? 'Guest' }}!</p>
                        <p>This is a Tabler-styled Livewire dashboard page. Menu links are ready (add controllers/routes
                            as needed).</p>
                        <div class="row row-cards">
                            <div class="col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Upload File</h3>
                                        <p>Placeholder for file upload feature.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Master Data</h3>
                                        <p>Placeholder for master data management.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Reports</h3>
                                        <p>Placeholder for reports and analytics.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>