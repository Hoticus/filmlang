import "./styles/app.sass"

import React from "react"
import ReactDOM from "react-dom/client"
import { BrowserRouter, Navigate, Route, Routes } from "react-router-dom"
import Header from "./components/Header"
import AuthenticationForm from "./components/AuthenticationForm"
import Home from "./pages/Home"
import FilmDetail from "./pages/FilmDetail"
import Spinner from "./components/Spinner"

const initialize = async () => {
  const authenticated = (await (await fetch("/api/is-authenticated")).json())
    .response

  class App extends React.Component {
    state = {
      configuration: null,
      authenticated,
    }

    constructor(props) {
      super(props)

      this.setAuthenticated = this.setAuthenticated.bind(this)
    }

    componentDidMount() {
      fetch("/api/get-tmdb-api-configuration")
        .then((res) => res.json())
        .then((res) => this.setState({ configuration: res.response }))
    }

    setAuthenticated(authenticated) {
      this.setState({ authenticated })
    }

    getLoader(marginTop = 12, marginBottom = 4) {
      return (
        <div className={`text-center mt-${marginTop} mb-${marginBottom}`}>
          <Spinner className="w-10 h-10" />
        </div>
      )
    }

    render() {
      const { authenticated, configuration } = this.state
      return (
        <>
          <Header authenticated={authenticated} />
          <main>
            <Routes>
              <Route
                path="/"
                element={
                  configuration ? (
                    <Home configuration={configuration} />
                  ) : (
                    this.getLoader()
                  )
                }
              />
              <Route
                path="film/:id"
                element={
                  configuration ? (
                    <FilmDetail configuration={configuration} />
                  ) : (
                    this.getLoader()
                  )
                }
              />
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
