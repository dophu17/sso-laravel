/**
 * Home Page - Public route
 */

import { useAuth } from '../hooks/useAuth';
import Link from 'next/link';

export default function Home() {
    const { user, authenticated, loading } = useAuth();

    if (loading) {
        return <div>Loading...</div>;
    }

    return (
        <div style={{ padding: '20px', fontFamily: 'Arial, sans-serif' }}>
            <h1>NextJS SSO Client</h1>
            
            {authenticated ? (
                <div>
                    <h2>Welcome back, {user.user_name}!</h2>
                    <p>Email: {user.user_email}</p>
                    <p>Role: {user.user_role}</p>
                    
                    <div style={{ marginTop: '20px' }}>
                        <Link href="/dashboard">
                            <button style={{ marginRight: '10px', padding: '10px 20px' }}>
                                Go to Dashboard
                            </button>
                        </Link>
                        
                        <Link href="/profile">
                            <button style={{ marginRight: '10px', padding: '10px 20px' }}>
                                View Profile
                            </button>
                        </Link>
                        
                        <button 
                            onClick={() => window.location.href = '/logout'}
                            style={{ padding: '10px 20px' }}
                        >
                            Logout
                        </button>
                    </div>
                </div>
            ) : (
                <div>
                    <h2>You are not logged in</h2>
                    <p>Please log in to access protected features.</p>
                    
                    <button 
                        onClick={() => window.location.href = '/login'}
                        style={{ padding: '10px 20px' }}
                    >
                        Login
                    </button>
                </div>
            )}
            
            <div style={{ marginTop: '30px', padding: '20px', backgroundColor: '#f5f5f5' }}>
                <h3>Available Routes:</h3>
                <ul>
                    <li><Link href="/">Home (Public)</Link></li>
                    <li><Link href="/dashboard">Dashboard (Protected)</Link></li>
                    <li><Link href="/profile">Profile (Protected)</Link></li>
                    <li><Link href="/admin">Admin (Protected)</Link></li>
                </ul>
            </div>
        </div>
    );
}
