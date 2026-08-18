import { NextRequest, NextResponse } from 'next/server';
import { verifyAdminCredentials, setAdminSessionCookie, clearAdminSessionCookie, getAdminSessionFromRequest } from '@/lib/auth';

export const dynamic = 'force-dynamic';

export async function POST(request: NextRequest) {
  try {
    const body = await request.json();
    const { username, password } = body;

    if (!username || !password) {
      return NextResponse.json(
        { success: false, error: 'Username and password are required' },
        { status: 400 }
      );
    }

    const sessionPayload = await verifyAdminCredentials(username, password);

    if (!sessionPayload) {
      return NextResponse.json(
        { success: false, error: 'Invalid username or password' },
        { status: 401 }
      );
    }

    const response = NextResponse.json({
      success: true,
      message: 'Authentication successful',
      user: {
        userId: sessionPayload.userId,
        username: sessionPayload.username,
        role: sessionPayload.role,
      },
    });

    return setAdminSessionCookie(response, {
      userId: sessionPayload.userId,
      username: sessionPayload.username,
      role: sessionPayload.role,
    });
  } catch (error: any) {
    console.error('Admin login error:', error);
    return NextResponse.json(
      { success: false, error: error.message || 'Login failed' },
      { status: 500 }
    );
  }
}

export async function GET(request: NextRequest) {
  try {
    const session = getAdminSessionFromRequest(request);
    if (!session) {
      return NextResponse.json({ authenticated: false }, { status: 401 });
    }

    return NextResponse.json({
      authenticated: true,
      user: {
        userId: session.userId,
        username: session.username,
        role: session.role,
      },
    });
  } catch (error: any) {
    return NextResponse.json({ authenticated: false }, { status: 500 });
  }
}

export async function DELETE() {
  const response = NextResponse.json({
    success: true,
    message: 'Logged out successfully',
  });
  return clearAdminSessionCookie(response);
}
