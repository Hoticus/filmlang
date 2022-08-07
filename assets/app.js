import React from "react"
import ReactDOM from "react-dom/client"
import { BrowserRouter, Navigate, Route, Routes } from "react-router-dom"
import Header from "./components/Header"
import AuthenticationForm from "./components/AuthenticationForm"
import Home from "./pages/Home"

import "./styles/app.sass"

const initialize = async () => {
  const authenticated = (await (await fetch("/api/is-authenticated")).json())
    .response

  class App extends React.Component {
    state = {
      authenticated: authenticated,
    }

    constructor(props) {
      super(props)

      this.setAuthenticated = this.setAuthenticated.bind(this)
    }

    setAuthenticated(authenticated) {
      this.setState({ authenticated: authenticated })
    }

    render() {
      const { authenticated } = this.state
      return (
        <>
          <Header authenticated={authenticated} />
          <main>
            <Routes>
              <Route path="/" element={<Home />} />
              {!authenticated && (
                <Route
                  path="authentication"
                  element={
                    <AuthenticationForm
                      setAuthenticated={this.setAuthenticated}
                    />
                  }
                />
              )}
              <Route path="*" element={<Navigate to="/" />} />
            </Routes>
          </main>
        </>
      )
    }
  }

  const root = ReactDOM.createRoot(document.getElementById("root"))
  root.render(
    <BrowserRouter>
      <App />
    </BrowserRouter>,
  )
}
initialize()
