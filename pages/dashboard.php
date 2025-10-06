<div class="row g-3">
  <div class="col-xl-3 col-md-6">
    <div class="card card-metric border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted">New Orders</div>
        <div class="display-6">128</div>
        <div class="text-success small">▲ 12% vs last week</div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card card-metric border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted">Revenue</div>
        <div class="display-6">$14,230</div>
        <div class="text-success small">▲ 5% vs last week</div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card card-metric border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted">Active Users</div>
        <div class="display-6">2,431</div>
        <div class="text-danger small">▼ 2% vs last week</div>
      </div>
    </div>
  </div>
  <div class="col-xl-3 col-md-6">
    <div class="card card-metric border-0 shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted">Bounce Rate</div>
        <div class="display-6">34%</div>
        <div class="text-success small">▲ 1.4% vs last week</div>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mt-1">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <strong>Performance</strong>
        <div class="btn-group btn-group-sm" role="group" aria-label="Time range">
          <button type="button" class="btn btn-outline-secondary active">7d</button>
          <button type="button" class="btn btn-outline-secondary">30d</button>
          <button type="button" class="btn btn-outline-secondary">90d</button>
        </div>
      </div>
      <div class="card-body">
        <div class="bg-light rounded-2 w-100" style="height: 300px; display: grid; place-items: center;">
          <span class="text-muted">Chart placeholder</span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card border-0 shadow-sm h-100">
      <div class="card-header bg-white"><strong>Tasks</strong></div>
      <div class="card-body">
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex align-items-center">
            <input class="form-check-input me-2" type="checkbox" value="" id="task1">
            Review orders requiring approval
          </li>
          <li class="list-group-item d-flex align-items-center">
            <input class="form-check-input me-2" type="checkbox" value="" id="task2">
            Update product inventory
          </li>
          <li class="list-group-item d-flex align-items-center">
            <input class="form-check-input me-2" type="checkbox" value="" id="task3">
            Prepare weekly sales report
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

<div class="card border-0 shadow-sm mt-3">
  <div class="card-header bg-white d-flex justify-content-between align-items-center">
    <strong>Recent Activity</strong>
    <div class="input-group input-group-sm" style="max-width: 260px;">
      <span class="input-group-text">Filter</span>
      <input type="text" class="form-control" placeholder="Type to filter..." aria-label="Filter table">
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th scope="col">#</th>
          <th scope="col">Type</th>
          <th scope="col">Details</th>
          <th scope="col">Owner</th>
          <th scope="col" class="text-end">Time</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th scope="row">1</th>
          <td><span class="badge text-bg-primary">Order</span></td>
          <td>Order <strong>#10234</strong> placed</td>
          <td>Alex Kim</td>
          <td class="text-end">2 min ago</td>
        </tr>
        <tr>
          <th scope="row">2</th>
          <td><span class="badge text-bg-success">Product</span></td>
          <td>Updated stock for <strong>SKU‑RC‑204</strong></td>
          <td>Jamie Lee</td>
          <td class="text-end">12 min ago</td>
        </tr>
        <tr>
          <th scope="row">3</th>
          <td><span class="badge text-bg-warning">Alert</span></td>
          <td>Low inventory threshold reached</td>
          <td>System</td>
          <td class="text-end">45 min ago</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
