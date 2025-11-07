const { useState, useEffect } = React;

// API Base URL
const API_URL = '/api';

// API Service
const api = {
    async get(endpoint) {
        const response = await fetch(`${API_URL}${endpoint}`);
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'API Error');
        }
        return data.data;
    },
    
    async post(endpoint, body = {}) {
        const response = await fetch(`${API_URL}${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'API Error');
        }
        return data.data;
    },
    
    async delete(endpoint) {
        const response = await fetch(`${API_URL}${endpoint}`, {
            method: 'DELETE'
        });
        const data = await response.json();
        if (!data.success) {
            throw new Error(data.message || 'API Error');
        }
        return data.data;
    }
};

// Header Component
function Header() {
    return (
        <div className="header">
            <h1>🖥️ Ubuntu Server Management Dashboard</h1>
            <p className="subtitle">Monitor and manage your Ubuntu server in real-time</p>
        </div>
    );
}

// System Info Component
function SystemInfo() {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            setLoading(true);
            const [systemInfo, cpuInfo, memoryInfo] = await Promise.all([
                api.get('/system/info'),
                api.get('/system/cpu'),
                api.get('/system/memory')
            ]);
            setData({ systemInfo, cpuInfo, memoryInfo });
            setError(null);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div className="loading"><div className="spinner"></div>Loading system information...</div>;
    if (error) return <div className="error">Error: {error}</div>;

    return (
        <div className="cards-grid">
            <div className="card">
                <h3>System Information</h3>
                <div className="card-content">
                    <div className="stat-row">
                        <span className="stat-label">Hostname:</span>
                        <span className="stat-value">{data.systemInfo.hostname}</span>
                    </div>
                    <div className="stat-row">
                        <span className="stat-label">OS:</span>
                        <span className="stat-value">{data.systemInfo.os}</span>
                    </div>
                    <div className="stat-row">
                        <span className="stat-label">Kernel:</span>
                        <span className="stat-value">{data.systemInfo.kernel}</span>
                    </div>
                    <div className="stat-row">
                        <span className="stat-label">Uptime:</span>
                        <span className="stat-value">{data.systemInfo.uptime}</span>
                    </div>
                    <div className="stat-row">
                        <span className="stat-label">Architecture:</span>
                        <span className="stat-value">{data.systemInfo.architecture}</span>
                    </div>
                </div>
            </div>

            <div className="card">
                <h3>CPU Information</h3>
                <div className="card-content">
                    <div className="stat-row">
                        <span className="stat-label">Cores:</span>
                        <span className="stat-value">{data.cpuInfo.cores}</span>
                    </div>
                    <div className="stat-row">
                        <span className="stat-label">Usage:</span>
                        <span className="stat-value">{data.cpuInfo.usage.toFixed(2)}%</span>
                    </div>
                </div>
            </div>

            <div className="card">
                <h3>Memory Information</h3>
                <div className="card-content">
                    <div className="stat-row">
                        <span className="stat-label">Total:</span>
                        <span className="stat-value">{data.memoryInfo.total_mb} MB</span>
                    </div>
                    <div className="stat-row">
                        <span className="stat-label">Used:</span>
                        <span className="stat-value">{data.memoryInfo.used_mb} MB</span>
                    </div>
                    <div className="stat-row">
                        <span className="stat-label">Free:</span>
                        <span className="stat-value">{data.memoryInfo.free_mb} MB</span>
                    </div>
                    <div className="stat-row">
                        <span className="stat-label">Usage:</span>
                        <span className="stat-value">{data.memoryInfo.usage_percent}%</span>
                    </div>
                </div>
            </div>
        </div>
    );
}

// Processes Component
function Processes() {
    const [processes, setProcesses] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        loadProcesses();
    }, []);

    const loadProcesses = async () => {
        try {
            setLoading(true);
            const data = await api.get('/processes?limit=20');
            setProcesses(data);
            setError(null);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    const killProcess = async (pid) => {
        if (!confirm(`Kill process ${pid}?`)) return;
        try {
            await api.delete(`/processes/${pid}`);
            loadProcesses();
        } catch (err) {
            alert('Error: ' + err.message);
        }
    };

    if (loading) return <div className="loading"><div className="spinner"></div>Loading processes...</div>;
    if (error) return <div className="error">Error: {error}</div>;

    return (
        <div className="data-table">
            <table>
                <thead>
                    <tr>
                        <th>PID</th>
                        <th>User</th>
                        <th>CPU%</th>
                        <th>MEM%</th>
                        <th>Command</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {processes.map(proc => (
                        <tr key={proc.pid}>
                            <td>{proc.pid}</td>
                            <td>{proc.user}</td>
                            <td>{proc.cpu}%</td>
                            <td>{proc.mem}%</td>
                            <td>{proc.command}</td>
                            <td>
                                <button className="btn btn-danger" onClick={() => killProcess(proc.pid)}>
                                    Kill
                                </button>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

// Services Component
function Services() {
    const [services, setServices] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        loadServices();
    }, []);

    const loadServices = async () => {
        try {
            setLoading(true);
            const data = await api.get('/services');
            setServices(data.slice(0, 20)); // Show first 20
            setError(null);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    const controlService = async (name, action) => {
        try {
            await api.post(`/services/${name}/${action}`);
            loadServices();
        } catch (err) {
            alert('Error: ' + err.message);
        }
    };

    if (loading) return <div className="loading"><div className="spinner"></div>Loading services...</div>;
    if (error) return <div className="error">Error: {error}</div>;

    return (
        <div className="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Load</th>
                        <th>Active</th>
                        <th>Sub</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {services.map(service => (
                        <tr key={service.name}>
                            <td>{service.name}</td>
                            <td><span className={`badge badge-${service.load === 'loaded' ? 'success' : 'danger'}`}>{service.load}</span></td>
                            <td><span className={`badge badge-${service.active === 'active' ? 'success' : 'danger'}`}>{service.active}</span></td>
                            <td>{service.sub}</td>
                            <td>
                                <div className="btn-group">
                                    <button className="btn btn-success" onClick={() => controlService(service.name, 'start')}>Start</button>
                                    <button className="btn btn-warning" onClick={() => controlService(service.name, 'restart')}>Restart</button>
                                    <button className="btn btn-danger" onClick={() => controlService(service.name, 'stop')}>Stop</button>
                                </div>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

// Disk Component
function Disk() {
    const [disks, setDisks] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        loadDisks();
    }, []);

    const loadDisks = async () => {
        try {
            setLoading(true);
            const data = await api.get('/disk/usage');
            setDisks(data);
            setError(null);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div className="loading"><div className="spinner"></div>Loading disk information...</div>;
    if (error) return <div className="error">Error: {error}</div>;

    return (
        <div className="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Filesystem</th>
                        <th>Size</th>
                        <th>Used</th>
                        <th>Available</th>
                        <th>Use%</th>
                        <th>Mounted On</th>
                    </tr>
                </thead>
                <tbody>
                    {disks.map((disk, idx) => (
                        <tr key={idx}>
                            <td>{disk.filesystem}</td>
                            <td>{disk.size}</td>
                            <td>{disk.used}</td>
                            <td>{disk.available}</td>
                            <td><span className={`badge ${parseInt(disk.use_percent) > 80 ? 'badge-danger' : 'badge-success'}`}>{disk.use_percent}</span></td>
                            <td>{disk.mounted_on}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

// Network Component
function Network() {
    const [connections, setConnections] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        loadConnections();
    }, []);

    const loadConnections = async () => {
        try {
            setLoading(true);
            const data = await api.get('/network/listening-ports');
            setConnections(data);
            setError(null);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div className="loading"><div className="spinner"></div>Loading network information...</div>;
    if (error) return <div className="error">Error: {error}</div>;

    return (
        <div className="data-table">
            <table>
                <thead>
                    <tr>
                        <th>Protocol</th>
                        <th>State</th>
                        <th>Local Address</th>
                        <th>Peer Address</th>
                    </tr>
                </thead>
                <tbody>
                    {connections.map((conn, idx) => (
                        <tr key={idx}>
                            <td>{conn.protocol}</td>
                            <td><span className="badge badge-info">{conn.state}</span></td>
                            <td>{conn.local_address}</td>
                            <td>{conn.peer_address}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

// SSH Terminal Component
function SSHTerminal() {
    const [command, setCommand] = useState('');
    const [output, setOutput] = useState([]);
    const [loading, setLoading] = useState(false);
    const [terminalInfo, setTerminalInfo] = useState(null);

    useEffect(() => {
        loadTerminalInfo();
    }, []);

    const loadTerminalInfo = async () => {
        try {
            const data = await api.get('/ssh/terminal-info');
            setTerminalInfo(data);
        } catch (err) {
            console.error('Failed to load terminal info:', err);
        }
    };

    const executeCommand = async (e) => {
        e.preventDefault();
        if (!command.trim()) return;

        setLoading(true);
        try {
            const result = await api.post('/ssh/execute', { command });
            setOutput(prev => [...prev, {
                command,
                output: result.output,
                success: result.success,
                timestamp: new Date().toLocaleTimeString()
            }]);
            setCommand('');
        } catch (err) {
            setOutput(prev => [...prev, {
                command,
                output: err.message,
                success: false,
                timestamp: new Date().toLocaleTimeString()
            }]);
        } finally {
            setLoading(false);
        }
    };

    const clearTerminal = () => {
        setOutput([]);
    };

    return (
        <div>
            {terminalInfo && (
                <div className="card" style={{marginBottom: '20px'}}>
                    <h3>Terminal Information</h3>
                    <div className="card-content">
                        <div className="stat-row">
                            <span className="stat-label">User:</span>
                            <span className="stat-value">{terminalInfo.user}</span>
                        </div>
                        <div className="stat-row">
                            <span className="stat-label">Shell:</span>
                            <span className="stat-value">{terminalInfo.shell}</span>
                        </div>
                        <div className="stat-row">
                            <span className="stat-label">Current Directory:</span>
                            <span className="stat-value">{terminalInfo.current_directory}</span>
                        </div>
                        <div className="stat-row">
                            <span className="stat-label">Hostname:</span>
                            <span className="stat-value">{terminalInfo.hostname}</span>
                        </div>
                    </div>
                </div>
            )}

            <div className="card">
                <h3>Terminal Output</h3>
                <div style={{
                    background: '#1a1a1a',
                    color: '#00ff00',
                    padding: '15px',
                    borderRadius: '8px',
                    fontFamily: 'monospace',
                    fontSize: '13px',
                    minHeight: '300px',
                    maxHeight: '500px',
                    overflowY: 'auto',
                    marginBottom: '15px'
                }}>
                    {output.length === 0 ? (
                        <div style={{color: '#666'}}>Execute commands to see output...</div>
                    ) : (
                        output.map((item, idx) => (
                            <div key={idx} style={{marginBottom: '15px', borderBottom: '1px solid #333', paddingBottom: '10px'}}>
                                <div style={{color: '#00aaff', marginBottom: '5px'}}>
                                    [{item.timestamp}] $ {item.command}
                                </div>
                                <pre style={{
                                    margin: 0,
                                    whiteSpace: 'pre-wrap',
                                    color: item.success ? '#00ff00' : '#ff4444'
                                }}>{item.output}</pre>
                            </div>
                        ))
                    )}
                </div>

                <form onSubmit={executeCommand} style={{display: 'flex', gap: '10px'}}>
                    <input
                        type="text"
                        value={command}
                        onChange={(e) => setCommand(e.target.value)}
                        placeholder="Enter command..."
                        disabled={loading}
                        style={{
                            flex: 1,
                            padding: '12px',
                            borderRadius: '6px',
                            border: '1px solid #ddd',
                            fontFamily: 'monospace'
                        }}
                    />
                    <button type="submit" className="btn btn-primary" disabled={loading}>
                        {loading ? 'Executing...' : 'Execute'}
                    </button>
                    <button type="button" className="btn btn-warning" onClick={clearTerminal}>
                        Clear
                    </button>
                </form>
            </div>
        </div>
    );
}

// Main App Component
function App() {
    const [activeTab, setActiveTab] = useState('system');

    const tabs = [
        { id: 'system', label: '🖥️ System Info', component: SystemInfo },
        { id: 'processes', label: '⚙️ Processes', component: Processes },
        { id: 'services', label: '🔧 Services', component: Services },
        { id: 'disk', label: '💾 Disk', component: Disk },
        { id: 'network', label: '🌐 Network', component: Network },
        { id: 'ssh', label: '💻 Terminal', component: SSHTerminal }
    ];

    const ActiveComponent = tabs.find(tab => tab.id === activeTab)?.component;

    return (
        <div>
            <Header />
            
            <div className="nav-tabs">
                {tabs.map(tab => (
                    <button
                        key={tab.id}
                        className={`nav-tab ${activeTab === tab.id ? 'active' : ''}`}
                        onClick={() => setActiveTab(tab.id)}
                    >
                        {tab.label}
                    </button>
                ))}
            </div>

            {ActiveComponent && <ActiveComponent />}
        </div>
    );
}

// Render the app
ReactDOM.render(<App />, document.getElementById('root'));
