/**
 * Profile Page - Protected route using getServerSideProps
 */

import { useAuth } from '../hooks/useAuth';
import Link from 'next/link';

export default function Profile({ user }) {
    const { logout } = useAuth();

    return (
        <div style={{ padding: '20px', fontFamily: 'Arial, sans-serif' }}>
            <h1>User Profile</h1>
            
            <div style={{ backgroundColor: '#e8f4f8', padding: '20px', borderRadius: '8px', marginBottom: '20px' }}>
                <h2>Profile Information</h2>
                <div style={{ display: 'grid', gap: '10px' }}>
                    <div>
                        <strong>Name:</strong> {user.user_name}
                    </div>
                    <div>
                        <strong>Email:</strong> {user.user_email}
                    </div>
                    <div>
                        <strong>Role:</strong> {user.user_role}
                    </div>
                    <div>
                        <strong>User ID:</strong> {user.user_id}
                    </div>
                    <div>
                        <strong>Login Time:</strong> {new Date(user.login_time).toLocaleString()}
                    </div>
                </div>
            </div>

            <div style={{ backgroundColor: '#fff3cd', padding: '15px', borderRadius: '5px', marginBottom: '20px' }}>
                <h3>Server-Side Authentication</h3>
                <p>This page uses <code>getServerSideProps</code> to verify authentication on the server side.</p>
                <p>User data is fetched during server-side rendering for better SEO and performance.</p>
            </div>

            <div style={{ marginBottom: '20px' }}>
                <Link href="/dashboard">
                    <button style={{ marginRight: '10px', padding: '10px 20px' }}>
                        ← Back to Dashboard
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
                <h3>Technical Details</h3>
                <ul>
                    <li>Authentication verified server-side</li>
                    <li>User data fetched during SSR</li>
                    <li>Automatic redirect to login if not authenticated</li>
                    <li>Session cookie shared across subdomains</li>
                </ul>
            </div>
        </div>
    );
}

// Server-side authentication check
export async function getServerSideProps(context) {
    const ssoService = (await import('../lib/sso')).default;
    
    try {
        const authResult = await ssoService.checkAuth(context);
        
        if (!authResult.authenticated) {
            // Redirect to SSO login
            const loginUrl = ssoService.getLoginUrl(context.resolvedUrl);
            
            return {
                redirect: {
                    destination: loginUrl,
                    permanent: false,
                },
            };
        }

        // Return user data as props
        return {
            props: {
                user: authResult.user,
            },
        };

    } catch (error) {
        console.error('Profile page auth check failed:', error);
        
        // Redirect to login on error
        const loginUrl = ssoService.getLoginUrl(context.resolvedUrl);
        
        return {
            redirect: {
                destination: loginUrl,
                permanent: false,
            },
        };
    }
}
