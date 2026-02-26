<?php
$url = 'http://127.0.0.1:8000/api/reports/etl';
$response = file_get_contents($url);
$data = json_decode($response, true);

$schoolYearsUrl = 'http://127.0.0.1:8000/api/school-years';
$schoolYearsResponse = file_get_contents($schoolYearsUrl);
$schoolYears = json_decode($schoolYearsResponse, true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test ETL Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .filter-section { margin-bottom: 20px; }
        .filter-group { margin-right: 20px; display: inline-block; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Test ETL Report</h1>
        
        <div class="filter-section">
            <div class="filter-group">
                <label for="schoolYear">School Year:</label>
                <select id="schoolYear">
                    <option value="">All School Years</option>
                    <?php foreach ($schoolYears as $sy): ?>
                        <option value="<?php echo $sy['id']; ?>">
                            <?php echo $sy['year']; ?> - <?php echo $sy['semester']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="role">Role:</label>
                <select id="role">
                    <option value="">All Roles</option>
                    <option value="Adviser">Adviser</option>
                    <option value="Chair">Chair</option>
                    <option value="Critique">Critique</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="status">Status:</label>
                <select id="status">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="minEtl">Min ETL:</label>
                <input type="number" id="minEtl" step="0.1" min="0">
            </div>
            
            <div class="filter-group">
                <label for="maxEtl">Max ETL:</label>
                <input type="number" id="maxEtl" step="0.1" min="0">
            </div>
            
            <div class="filter-group">
                <label for="search">Search:</label>
                <input type="text" id="search">
            </div>
            
            <button onclick="applyFilters()">Apply Filters</button>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Roles</th>
                    <th>Status</th>
                    <th>Groups</th>
                    <th>ETL Total</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php foreach ($data['data']['panel_members'] as $member): ?>
                    <tr>
                        <td><?php echo $member['name']; ?></td>
                        <td><?php echo implode(', ', $member['roles']); ?></td>
                        <td><?php echo $member['status']; ?></td>
                        <td><?php echo $member['groups_count']; ?></td>
                        <td><?php echo $member['etl_total']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div id="summary" style="margin-top: 20px;">
            <p>Total Members: <?php echo $data['data']['summary']['total_panel_members']; ?></p>
            <p>Total ETL: <?php echo $data['data']['summary']['total_etl']; ?></p>
            <p>Average ETL: <?php echo $data['data']['summary']['average_etl']; ?></p>
            <p>Total Groups: <?php echo $data['data']['summary']['total_groups']; ?></p>
        </div>
    </div>

    <script>
        const allData = <?php echo json_encode($data['data']['panel_members']); ?>;
        const schoolYears = <?php echo json_encode($schoolYears); ?>;
        
        // Apply filters to the data
        function applyFilters() {
            let data = [...allData];
            
            const roleFilter = document.getElementById('role').value;
            if (roleFilter) {
                data = data.filter(m => m.roles && m.roles.includes(roleFilter));
            }
            
            const statusFilter = document.getElementById('status').value;
            if (statusFilter) {
                data = data.filter(m => m.status === statusFilter);
            }
            
            const minEtl = document.getElementById('minEtl').value;
            if (minEtl !== '') {
                data = data.filter(m => m.etl_total >= parseFloat(minEtl));
            }
            
            const maxEtl = document.getElementById('maxEtl').value;
            if (maxEtl !== '') {
                data = data.filter(m => m.etl_total <= parseFloat(maxEtl));
            }
            
            const searchFilter = document.getElementById('search').value.toLowerCase();
            if (searchFilter) {
                data = data.filter(m => m.name.toLowerCase().includes(searchFilter));
            }
            
            renderData(data);
            updateSummary(data);
        }
        
        // Render the data in the table
        function renderData(data) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            
            data.forEach(member => {
                const row = tbody.insertRow();
                
                const nameCell = row.insertCell();
                nameCell.textContent = member.name;
                
                const rolesCell = row.insertCell();
                rolesCell.textContent = member.roles.join(', ');
                
                const statusCell = row.insertCell();
                statusCell.textContent = member.status;
                
                const groupsCell = row.insertCell();
                groupsCell.textContent = member.groups_count;
                
                const etlCell = row.insertCell();
                etlCell.textContent = member.etl_total.toFixed(2);
            });
        }
        
        // Update the summary stats
        function updateSummary(data) {
            const totalMembers = data.length;
            const totalEtl = data.reduce((sum, m) => sum + m.etl_total, 0);
            const averageEtl = totalMembers > 0 ? totalEtl / totalMembers : 0;
            const totalGroups = data.reduce((sum, m) => sum + (m.groups_count || 0), 0);
            
            document.getElementById('summary').innerHTML = `
                <p>Total Members: ${totalMembers}</p>
                <p>Total ETL: ${totalEtl.toFixed(2)}</p>
                <p>Average ETL: ${averageEtl.toFixed(2)}</p>
                <p>Total Groups: ${totalGroups}</p>
            `;
        }
        
        // Event listener for school year filter
        document.getElementById('schoolYear').addEventListener('change', function() {
            const schoolYearId = this.value;
            window.location.href = `?school_year=${schoolYearId}`;
        });
    </script>
</body>
</html>
