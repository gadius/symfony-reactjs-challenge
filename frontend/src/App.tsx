import { useState } from 'react';

interface SumResponse {
  sum: number;
}
interface ErrorResponse {
  error: string;
}

function App() {
  const URL = 'http://localhost:8080/api/sum';
  const SHOW_BACKEND_ERRORS = true; // For demonstration, bypassing frontend validation to showcase backend error handling
  const [a, setA] = useState<string>('');
  const [b, setB] = useState<string>('');
  const [result, setResult] = useState<number | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState<boolean>(false);

  const resetStates = () => {
    setResult(null);
    setError(null);
  }

  const isValidInput = (a: string, b: string): boolean => {
    if (a === '' || b === '') return false;
    
    const numA = parseFloat(a);
    const numB = parseFloat(b);
    
    return !isNaN(numA) && !isNaN(numB);
  }

  const handleCalculate = async () => {
    resetStates();
    
    if(!SHOW_BACKEND_ERRORS){
      if (!isValidInput(a, b)) {
        if (a === '' || b === '') {
          setError('Both inputs are required');
        } else {
          setError('Both inputs must be valid numbers');
        }
        return;
      } 
    }

    setLoading(true);  

    try {      
      const response = await fetch(URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          a: parseFloat(a),
          b: parseFloat(b),
        }),
      });
      
      const data: SumResponse | ErrorResponse = await response.json();

      if (response.ok) {
        setResult((data as SumResponse).sum);
      } else {
        setError((data as ErrorResponse).error || 'An error occurred');
      }
    } catch (err: unknown) {
      const message = err instanceof Error ? err.message : 'An unexpected error occurred';
      setError(message);
    } finally {
      setLoading(false);
    }
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Enter' && isValidInput(a, b) && !loading) {
      handleCalculate();
    }
  };

  
  const isValid = SHOW_BACKEND_ERRORS ? true : isValidInput(a, b);

  return (
    <div className="min-h-screen bg-gradient-to-br from-indigo-500 flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <div className="bg-white rounded-2xl shadow-2xl p-8">
          <div className="text-center mb-8">
            <h1 className="text-3xl font-bold text-gray-800 mb-2">
              Sum Calculator
            </h1>
            <p className="text-gray-600 text-sm">
              Enter two numbers to calculate their sum
            </p>
          </div>

          <div className="space-y-4 mb-6">
            <div>
              <label
                htmlFor="input-a"
                className="block text-sm font-semibold text-gray-700 mb-2"
              >
                Number A
              </label>
              <input
                id="input-a"
                type="number"
                value={a}
                onChange={(e) => setA(e.target.value)}
                onKeyDown={handleKeyDown}
                placeholder="Enter first number"
                disabled={loading}
                className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all disabled:bg-gray-100 disabled:cursor-not-allowed"
              />
            </div>

            <div>
              <label
                htmlFor="input-b"
                className="block text-sm font-semibold text-gray-700 mb-2"
              >
                Number B
              </label>
              <input
                id="input-b"
                type="number"
                value={b}
                onChange={(e) => setB(e.target.value)}
                onKeyDown={handleKeyDown}
                placeholder="Enter second number"
                disabled={loading}
                className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 outline-none transition-all disabled:bg-gray-100 disabled:cursor-not-allowed"
              />
            </div>
          </div>

          <button
            onClick={handleCalculate}
            disabled={loading || !isValid}
            className="w-full py-3 px-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-md"
          >
            {loading ? (
              <span className="flex items-center justify-center">
                <svg
                  className="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    className="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    strokeWidth="4"
                  ></circle>
                  <path
                    className="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                Calculating...
              </span>
            ) : (
              'Calculate'
            )}
          </button>

          {result !== null && (
            <div className="mt-6 p-4 bg-green-50 border-2 border-green-200 rounded-lg animate-fade-in">
              <p className="text-sm font-semibold text-green-800 mb-1">Result</p>
              <p className="text-3xl font-bold text-green-600">{result}</p>
            </div>
          )}

          {error && (
            <div className="mt-6 p-4 bg-red-50 border-2 border-red-200 rounded-lg animate-fade-in">
              <p className="text-sm font-semibold text-red-800 mb-1">Error</p>
              <p className="text-red-600">{error}</p>
            </div>
          )}
        </div>

        <div className="text-center mt-6">
          <p className="text-white text-sm">
            Developer: Gad Diego Andres Sanchez Ortega
          </p>
        </div>
      </div>
    </div>
  );
}

export default App;
