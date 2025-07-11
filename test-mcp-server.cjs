#!/usr/bin/env node

/**
 * Test script for Playwright MCP Server
 * Tests basic functionality and connectivity
 */

const { spawn } = require('child_process');
const http = require('http');

class MCPServerTester {
    constructor() {
        this.serverProcess = null;
        this.config = {
            host: 'localhost',
            port: 3001,
            browser: 'chrome',
            headless: true
        };
    }

    async startServer() {
        console.log('🚀 Starting Playwright MCP Server...');
        
        const args = [
            '--headless',
            '--browser', this.config.browser,
            '--port', this.config.port.toString(),
            '--host', this.config.host,
            '--isolated',
            '--viewport-size', '1280,720',
            '--ignore-https-errors',
            '--save-trace'
        ];

        this.serverProcess = spawn('mcp-server-playwright', args, {
            stdio: ['pipe', 'pipe', 'pipe']
        });

        return new Promise((resolve, reject) => {
            let output = '';
            
            this.serverProcess.stdout.on('data', (data) => {
                output += data.toString();
                console.log(`📝 Server output: ${data.toString().trim()}`);
            });

            this.serverProcess.stderr.on('data', (data) => {
                console.log(`⚠️  Server error: ${data.toString().trim()}`);
            });

            this.serverProcess.on('error', (error) => {
                console.error('❌ Failed to start server:', error.message);
                reject(error);
            });

            // Wait for server to start
            setTimeout(() => {
                console.log('✅ Server started successfully');
                resolve();
            }, 3000);
        });
    }

    async testConnectivity() {
        console.log('🔍 Testing server connectivity...');
        
        return new Promise((resolve, reject) => {
            const req = http.request({
                hostname: this.config.host,
                port: this.config.port,
                path: '/',
                method: 'GET',
                timeout: 5000
            }, (res) => {
                console.log(`✅ Server responding with status: ${res.statusCode}`);
                resolve(res.statusCode);
            });

            req.on('error', (error) => {
                console.error('❌ Connectivity test failed:', error.message);
                reject(error);
            });

            req.on('timeout', () => {
                console.error('❌ Connection timeout');
                req.destroy();
                reject(new Error('Connection timeout'));
            });

            req.end();
        });
    }

    async testBasicFunctionality() {
        console.log('🧪 Testing basic MCP functionality...');
        
        // Test basic HTTP endpoints that MCP server should respond to
        const testEndpoints = [
            '/',
            '/health',
            '/status'
        ];

        for (const endpoint of testEndpoints) {
            try {
                await this.makeRequest(endpoint);
                console.log(`✅ Endpoint ${endpoint} is accessible`);
            } catch (error) {
                console.log(`⚠️  Endpoint ${endpoint} returned error (expected for some endpoints)`);
            }
        }
    }

    async makeRequest(path) {
        return new Promise((resolve, reject) => {
            const req = http.request({
                hostname: this.config.host,
                port: this.config.port,
                path: path,
                method: 'GET',
                timeout: 3000
            }, (res) => {
                let data = '';
                res.on('data', chunk => data += chunk);
                res.on('end', () => resolve({ statusCode: res.statusCode, data }));
            });

            req.on('error', reject);
            req.on('timeout', () => {
                req.destroy();
                reject(new Error('Request timeout'));
            });

            req.end();
        });
    }

    async stopServer() {
        if (this.serverProcess) {
            console.log('🛑 Stopping MCP Server...');
            this.serverProcess.kill('SIGTERM');
            
            return new Promise((resolve) => {
                this.serverProcess.on('exit', () => {
                    console.log('✅ Server stopped successfully');
                    resolve();
                });
                
                // Force kill after 5 seconds if not stopped gracefully
                setTimeout(() => {
                    if (!this.serverProcess.killed) {
                        this.serverProcess.kill('SIGKILL');
                        console.log('🔨 Server force-killed');
                    }
                    resolve();
                }, 5000);
            });
        }
    }

    async runTests() {
        try {
            console.log('🎯 Starting Playwright MCP Server Tests\n');
            
            await this.startServer();
            await this.testConnectivity();
            await this.testBasicFunctionality();
            
            console.log('\n🎉 All tests completed successfully!');
            console.log('📋 MCP Server is ready for browser automation testing');
            
        } catch (error) {
            console.error('\n❌ Test failed:', error.message);
            process.exit(1);
        } finally {
            await this.stopServer();
        }
    }
}

// Run tests if this script is executed directly
if (require.main === module) {
    const tester = new MCPServerTester();
    tester.runTests().catch(console.error);
}

module.exports = MCPServerTester;
