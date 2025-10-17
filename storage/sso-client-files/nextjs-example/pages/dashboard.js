/**
 * Dashboard Page - Protected route
 */

import { useAuth } from '../hooks/useAuth';
import { withAuth } from '../hooks/useAuth';
import Link from 'next/link';

function Dashboard() {
    const { user, logout } = useAuth();

    return (
        <div style={{ padding: '20px', fontFamily: 'Arial, sans-serif' }}>
            <h1>Dashboard</h1>
            
            <div style={{ backgroundColor: '#e8f5e8', padding: '15px', borderRadius: '5px', marginBottom: '20px' }}>
                <h2>Welcome to Dashboard, {user.user_name}!</h2>
                <p><strong>Email:</strong> {user.user_email}</p>
                <p><strong>Role:</strong> {user.user_role}</p>
                <p><strong>User ID:</strong> {user.user_id}</p>
                <p><strong>Login Time:</strong> {new Date(user.login_time).toLocaleString()}</p>
            </div>

            <div style={{ marginBottom: '20px' }}>
                <h3>Quick Actions:</h3>
                <Link href="/profile">
                    <button style={{ marginRight: '10px', padding: '10px 20px' }}>
                        View Profile
                    </button>
                </Link>
                
                <Link href="/admin">
                    <button style={{ marginRight: '10px', padding: '10px 20px' }}>
                        Admin Panel
                    </button>
                </Link>
                
                <button 
                    onClick={() => logout()}
                    style={{ padding: '10px 20px', backgroundColor: '#dc3545', color: 'white', border: 'none', borderRadius: '3px' }}
                >
                    Logout
                </button>
            </div>

            <div style={{ backgroundColor: '#f8f9fa', padding: '15px', borderRadius: '5px' }}>
                <h3>Protected Content</h3>
                <p>This is a protected route that requires authentication.</p>
                <p>The middleware automatically redirected you here because you're logged in.</p>
                <p>If you weren't logged in, you would be redirected to the SSO login page.</p>
            </div>

            <div style={{ marginTop: '20px' }}>
                <Link href="/">
                    <button style={{ padding: '10px 20px' }}>
                        ← Back to Home
                    </button>
                </Link>
            </div>
        </div>
    );
}

// Wrap with authentication HOC
export default withAuth(Dashboard);
